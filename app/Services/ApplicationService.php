<?php

namespace App\Services;

use App\Models\Application;
use App\Models\User;
use App\Models\Internship;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApplicationService
{
    /**
     * Create a new internship application with transaction
     * 
     * @param int $userId
     * @param int $internshipId
     * @param string|null $motivationLetter
     * @return Application
     * @throws \Exception
     */
    public function createApplication(int $userId, int $internshipId, ?string $motivationLetter = null): Application
    {
        return DB::transaction(function () use ($userId, $internshipId, $motivationLetter) {
            // a) Pārbauda vai lietotājs ir datubāzē
            $user = $this->validateUser($userId);
            
            // b) Pārbauda vai prakse ir derīga
            $internship = $this->validateInternship($internshipId);
            
            // c) Pārbauda vai lietotājam atļauts pieteikties šajā praksē
            $this->validateApplicationEligibility($user, $internship);
            
            // Izveido prakses pieteikumu
            $application = Application::create([
                'user_id' => $userId,
                'internship_id' => $internshipId,
                'motivation_letter' => $motivationLetter,
                'is_approved' => false,
                'approved_at' => null,
            ]);
            
            return $application;
        });
    }

    /**
     * Validate that user exists in database
     * 
     * @param int $userId
     * @return User
     * @throws \Exception
     */
    private function validateUser(int $userId): User
    {
        $user = User::find($userId);
        
        if (!$user) {
            throw new \Exception('Lietotājs netika atrasts datubāzē.');
        }
        
        return $user;
    }

    /**
     * Validate that internship is valid (exists and is active)
     * 
     * @param int $internshipId
     * @return Internship
     * @throws \Exception
     */
    private function validateInternship(int $internshipId): Internship
    {
        $internship = Internship::find($internshipId);
        
        if (!$internship) {
            throw new \Exception('Prakse netika atrasta.');
        }
        
        // Pārbauda vai prakse ir aktīva (šobrīdējais datums ir starp sākuma un beigu datumu)
        $now = Carbon::now();
        
        if ($now->lt($internship->start_at)) {
            throw new \Exception('Prakse vēl nav sākusies.');
        }
        
        if ($now->gt($internship->end_at)) {
            throw new \Exception('Prakse ir beigusies.');
        }
        
        return $internship;
    }

    /**
     * Validate that user is eligible to apply for this internship
     * 
     * @param User $user
     * @param Internship $internship
     * @throws \Exception
     */
    private function validateApplicationEligibility(User $user, Internship $internship): void
    {
        // Pārbauda vai lietotājs jau nav pieteicies šai praksei
        $existingApplication = Application::where('user_id', $user->id)
            ->where('internship_id', $internship->id)
            ->first();
        
        if ($existingApplication) {
            throw new \Exception('Jūs jau esat pieteicies šai praksei.');
        }
        
        // Pārbauda vai lietotājs ir studenta lomā (var pielāgot pēc vajadzības)
        if ($user->role_id && $user->role->name !== 'students') {
            throw new \Exception('Tikai studenti var pieteikties praksēm.');
        }
        
        // Pārbauda vai lietotājs nav jau apstiprināts citai praksei šajā periodā
        $conflictingApplication = Application::where('user_id', $user->id)
            ->where('is_approved', true)
            ->whereHas('internship', function ($query) use ($internship) {
                $query->where('start_at', '<=', $internship->end_at)
                    ->where('end_at', '>=', $internship->start_at);
            })
            ->first();
        
        if ($conflictingApplication) {
            throw new \Exception('Jūs jau esat apstiprināts citai praksei šajā laika periodā.');
        }
    }
}

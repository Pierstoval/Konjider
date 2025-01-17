<?php

namespace App\Entity;

use App\Enum\ScheduleAnimationState;
use App\Repository\ScheduledAnimationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScheduledAnimationRepository::class)]
class ScheduledAnimation
{
    use Field\Id;

    #[ORM\Column(type: 'string', length: 255, enumType: ScheduleAnimationState::class)]
    #[Assert\NotBlank]
    private ScheduleAnimationState $state = ScheduleAnimationState::CREATED;

    #[ORM\ManyToOne(inversedBy: 'scheduledAnimations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Animation $animation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?TimeSlot $timeSlot = null;

    public function getState(): ScheduleAnimationState
    {
        return $this->state;
    }

    public function setState(ScheduleAnimationState $state): void
    {
        $this->state = $state;
    }

    public function getAnimation(): ?Animation
    {
        return $this->animation;
    }

    public function setAnimation(Animation $animation): void
    {
        $this->animation = $animation;
    }

    public function getTimeSlot(): ?TimeSlot
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(?TimeSlot $timeSlot): void
    {
        $this->timeSlot = $timeSlot;
    }
}

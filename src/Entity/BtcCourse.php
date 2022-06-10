<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\BtcCourseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BtcCourseRepository::class)
 */
class BtcCourse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $currency;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $time;

    /**
     * @ORM\Column(type="float")
     */
    private $high;

    /**
     * @ORM\Column(type="float")
     */
    private $low;

    /**
     * @ORM\Column(type="float")
     */
    private $open;

    /**
     * @ORM\Column(type="float")
     */
    private $close;

    public function __construct(
        string $currency,
        \DateTimeImmutable $time,
        float $high,
        float $low,
        float $open,
        float $close)
    {
        $this->currency = $currency;
        $this->time = $time;
        $this->high = $high;
        $this->low = $low;
        $this->open = $open;
        $this->close = $close;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getTime(): \DateTimeImmutable
    {
        return $this->time;
    }
}

<?php

namespace App\DTO;

/**
 * Class EmailDTO
 *
 * The EmailDTO class is a Data Transfer Object used for encapsulating email-related data.
 * It contains properties for the sender's name and phone number, providing a structured
 * way to pass this data within the application.
 *
 * @package App\DTO
 */
class EmailDTO
{
    /**
     * @var string The name of the sender.
     */
    public string $name;
    /**
     * @var string The phone number of the sender.
     */
    public string $phone;

    /**
     * EmailDTO constructor.
     *
     * Initializes a new instance of the EmailDTO class with the specified name and phone number.
     *
     * @param string $name  The name of the sender.
     * @param string $phone The phone number of the sender.
     */
    public function __construct(string $name, string $phone)
    {
        $this->name = $name;
        $this->phone = $phone;
    }
}

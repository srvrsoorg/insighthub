<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class UpdatedServerExist implements Rule
{
    private $user, $userServer;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user, $userServer)
    {
        $this->user = $user;
        $this->userServer = $userServer;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->userServer->server_id != $value) {
            return !$this->user->userServers()->where('server_id',$value)->exists();
        }
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This server permission already exists.';
    }
}

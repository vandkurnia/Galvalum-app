<?php

namespace App\Http\Livewire\User;

use Livewire\Component;

class EditUser extends Component
{

    public $hash_id_user;

    public function render()
    {
        return view('livewire.user.edit-user');
    }
}

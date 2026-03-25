<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class SitesIndexPage extends Component
{
    public function render()
    {
        $user = request()->user();

        abort_if(! $user, 403);

        return view('livewire.admin.pages.sites-index', [
            'currentAdminUser' => $user->toArray(),
        ]);
    }
}


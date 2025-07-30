<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatusBadge extends Component
{
    public $status;
    public $badgeClass;
    public $iconClass;

    public function __construct($status)
    {
        $this->status = $status;
        $statusLower = strtolower($status);

        switch ($statusLower) {
            case 'tersedia':
                $this->badgeClass = 'badge-success';
                $this->iconClass = 'fa fa-check-circle';
                break;
            case 'digunakan':
                $this->badgeClass = 'badge-primary';
                $this->iconClass = 'fa fa-user-check';
                break;
            case 'dalam perbaikan':
                $this->badgeClass = 'badge-warning';
                $this->iconClass = 'fa fa-tools';
                break;
            case 'rusak':
            case 'proses penghapusan':
                $this->badgeClass = 'badge-danger';
                $this->iconClass = 'fa fa-exclamation-triangle';
                break;
            default:
                $this->badgeClass = 'badge-secondary';
                $this->iconClass = 'fa fa-question-circle';
                break;
        }
    }

    public function render()
    {
        return view('components.status-badge');
    }
}
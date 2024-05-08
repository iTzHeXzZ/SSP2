<?php

namespace App\Exports;

use App\Models\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectsExport implements FromCollection,WithHeadings
{
    protected $projects;

    public function __construct(Collection $projects)
    {
        $this->projects = $projects;
    }

    public function collection()
    {
        return $this->projects->map(function ($project) {
            unset($project['bestand']);
            unset($project['created_at']);
            unset($project['updated_at']);
            return $project;
        });
    }


    public function headings(): array
    {
        return [            
        'ID',
        'Ort',
        'Postleitzahl',
        'Straße',
        'Hausnummer',
        'Wohneinheiten',
        'Status',
        'Notiz' ];
    }
}


<?php

namespace App\Imports;

use App\Models\Project;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProjectsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $existingProject = Project::where('ort', $row['ort'])
            ->where('postleitzahl', $row['postleitzahl'])
            ->first();
    
        
        if ($existingProject) {
            
            $existingProject = Project::where('ort', $row['ort'])
                ->where('postleitzahl', $row['postleitzahl'])
                ->where('strasse', $row['strasse'])
                ->where('hausnummer', $row['hausnummer'])
                ->first();
    
            
            if ($existingProject) {
                
                return null;
            }
        }
    
        $status = !empty($row['status']) ? $row['status'] : 'Unbesucht';
        $status = $row['bestand'] >= 1 ? 'Vertrag' : $row['status'];
    
        return new Project([
            'ort' => $row['ort'],
            'postleitzahl' => $row['postleitzahl'],
            'strasse' => $row['strasse'],
            'hausnummer' => $row['hausnummer'],
            'wohneinheiten' => $row['wohneinheiten'],
            'bestand' => $row['bestand'],
            'notiz' => $row['notiz'],
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function rules(): array
    {
        return [
            'ort' => 'required',
            'postleitzahl' => 'required',
            'strasse' => 'required',
            'hausnummer' => 'required',
            'wohneinheiten' => 'required',
            'bestand' => 'required',
            'notiz' => 'nullable',
            'status' => ['nullable', Rule::in(['Vertrag', 'Kein Interesse', 'Überleger', 'Unbesucht'])],
        ];
    }
}


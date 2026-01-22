<?php

namespace App\Traits;

use App\Models\CompanyData;
use Illuminate\Support\Facades\Log;

trait HandlesCompanyData
{
    protected function getCompanyData()
    {
        if (!$this->companyData) {
            try {
                $this->companyData = CompanyData::first();
                if (!$this->companyData) {
                    Log::warning('CompanyData not found in database, creating default record');
                    // Crear un registro por defecto si no existe
                    $this->companyData = CompanyData::create([
                        'company_name' => 'V General Contractors',
                        'company_email' => 'info@vgeneralcontractors.com',
                        'company_phone' => '(713) 587-6423',
                        'company_address' => '1302 Waugh Dr # 810 Houston TX 77019',
                        // otros campos necesarios...
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error retrieving CompanyData: ' . $e->getMessage());
                throw new \RuntimeException('Error retrieving company data: ' . $e->getMessage());
            }
        }
        return $this->companyData;
    }

    public function __sleep()
    {
        // Cuando Laravel serializa la notificación, solo guardamos el ID de CompanyData
        if ($this->companyData) {
            $this->companyDataId = $this->companyData->id;
        }
        return ['appointment', 'isInternal', 'companyDataId'];
    }

    public function __wakeup()
    {
        // Cuando Laravel deserializa la notificación, recuperamos CompanyData
        if (isset($this->companyDataId)) {
            try {
                $this->companyData = CompanyData::find($this->companyDataId);
                if (!$this->companyData) {
                    Log::warning('CompanyData not found during deserialization, attempting to get first record');
                    $this->companyData = CompanyData::first();
                }
                unset($this->companyDataId);
            } catch (\Exception $e) {
                Log::error('Error during CompanyData deserialization: ' . $e->getMessage());
                throw new \RuntimeException('Error during company data deserialization: ' . $e->getMessage());
            }
        }
    }
} 
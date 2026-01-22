<?php

namespace App\Traits;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait EmailValidation
{
    /**
     * Define el modelo a utilizar para las validaciones
     * Se debe sobrescribir en la clase que use este trait
     */
    protected function getValidationModel()
    {
        // Este método debe ser implementado en la clase que utilice el trait
        return isset($this->validationModel) ? $this->validationModel : null;
    }

    /**
     * Define la tabla a utilizar para las validaciones
     * Se puede sobrescribir en la clase que use este trait
     */
    protected function getValidationTable()
    {
        // Por defecto, intentamos obtener la tabla del modelo
        $model = $this->getValidationModel();
        return isset($this->validationTable) ? $this->validationTable : 
               ($model ? app($model)->getTable() : 'email_data');
    }

    /**
     * Define el campo UUID para validaciones
     */
    protected function getUuidField()
    {
        return isset($this->uuidField) ? $this->uuidField : 'uuid';
    }

    /**
     * Reglas de validación básicas para email y teléfono
     */
    protected function getValidationRules()
    {
        return [
            'description' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'type' => ['required', 'string'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }

    /**
     * Reglas de validación para creación
     */
    protected function getCreateValidationRules()
    {
        $table = $this->getValidationTable();
        $rules = $this->getValidationRules();
        
        // Agregar reglas unique solo si el campo existe en el formulario
        if (isset($rules['email'])) {
            $rules['email'][] = Rule::unique($table, 'email');
        }
        
        if (isset($rules['phone'])) {
            $rules['phone'][] = Rule::unique($table, 'phone');
        }
        
        return $rules;
    }

    /**
     * Reglas de validación para actualización
     */
    protected function getUpdateValidationRules()
    {
        $table = $this->getValidationTable();
        $uuidField = $this->getUuidField();
        $rules = $this->getValidationRules();
        
        // Agregar reglas unique con ignore solo si el campo existe en el formulario
        if (isset($rules['email'])) {
            $rules['email'][] = Rule::unique($table, 'email')->ignore($this->uuid, $uuidField);
        }
        
        if (isset($rules['phone'])) {
            $rules['phone'][] = Rule::unique($table, 'phone')->ignore($this->uuid, $uuidField);
        }
        
        return $rules;
    }

    /**
     * Verificar si un email ya existe en la base de datos
     */
    public function checkEmailExists($email)
    {
        try {
            if (empty($email)) {
                return false;
            }

            $table = $this->getValidationTable();
            $uuidField = $this->getUuidField();
            $model = $this->getValidationModel();
            
            // Crear una instancia del modelo
            $modelInstance = app($model);

            // Si estamos actualizando y el email no ha cambiado, es válido
            if (isset($this->modalAction) && $this->modalAction === 'update' && isset($this->uuid)) {
                $record = $modelInstance->where($uuidField, $this->uuid)->first();
                if ($record && $record->email === $email) {
                    return false;
                }
            }

            // Verificar si el email existe, excluyendo el registro actual si estamos actualizando
            return $modelInstance->where('email', $email)
                ->when(isset($this->uuid), function ($query) use ($uuidField) {
                    return $query->where($uuidField, '!=', $this->uuid);
                })
                ->exists();
        } catch (\Exception $e) {
            Log::error('Error en checkEmailExists', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Verificar si un teléfono ya existe en la base de datos
     */
    public function checkPhoneExists($phone)
    {
        try {
            if (empty($phone)) {
                return false;
            }

            $table = $this->getValidationTable();
            $uuidField = $this->getUuidField();
            $model = $this->getValidationModel();
            
            // Crear una instancia del modelo
            $modelInstance = app($model);
            
            // Formatear el teléfono si existe el método
            $formattedPhone = method_exists($this, 'formatPhone') ? 
                $this->formatPhone($phone) : $phone;

            // Si estamos actualizando y el teléfono no ha cambiado, es válido
            if (isset($this->modalAction) && $this->modalAction === 'update' && isset($this->uuid)) {
                $record = $modelInstance->where($uuidField, $this->uuid)->first();
                if ($record && $record->phone === $formattedPhone) {
                    return false;
                }
            }

            // Verificar si el teléfono existe, excluyendo el registro actual si estamos actualizando
            return $modelInstance->where('phone', $formattedPhone)
                ->when(isset($this->uuid), function ($query) use ($uuidField) {
                    return $query->where($uuidField, '!=', $this->uuid);
                })
                ->exists();
        } catch (\Exception $e) {
            Log::error('Error en checkPhoneExists', [
                'phone' => $phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Mensajes de validación personalizados
     */
    protected function getValidationMessages()
    {
        return [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'Por favor ingrese una dirección de email válida',
            'email.max' => 'El email no debe exceder 255 caracteres',
            'email.unique' => 'Este email ya está en uso',
            'phone.unique' => 'Este teléfono ya está en uso',
            'phone.max' => 'El teléfono no debe exceder 20 caracteres',
            'description.max' => 'La descripción no debe exceder 255 caracteres',
            'type.required' => 'El tipo es obligatorio',
            'user_id.required' => 'El usuario es obligatorio',
            'user_id.exists' => 'El usuario seleccionado no existe',
        ];
    }
}
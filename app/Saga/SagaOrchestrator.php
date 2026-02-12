<?php
namespace App\Saga;

use App\Models\SagaLog;
use App\Saga\Interfaces\SagaStep;
use Illuminate\Support\Str;
use Exception;

class SagaOrchestrator
{
    protected array $steps = [];
    protected array $completedSteps = [];
    protected string $traceId;

    public function __construct()
    {
        $this->traceId = (string) Str::uuid();
    }

    public function addStep(SagaStep $step): self
    {
        $this->steps[] = $step;
        return $this;
    }

    public function run(array $data): array
    {
        foreach ($this->steps as $step) {
            $stepName = class_basename($step);
            try {
                // Tenta executar
                $success = $step->execute($data);
                $this->log($stepName, 'execute', $success ? 'success' : 'failure', $data);

                if (!$success) {
                    throw new Exception("Falha no passo $stepName");
                }
                $this->completedSteps[] = $step;

            } catch (Exception $e) {
                // Se der erro, inicia o rollback
                $this->rollback($data);
                return ['status' => 'error', 'trace_id' => $this->traceId, 'message' => $e->getMessage()];
            }
        }
        return ['status' => 'success', 'trace_id' => $this->traceId];
    }

    protected function rollback(array $data): void
    {
        $stepsToRollback = array_reverse($this->completedSteps);
        foreach ($stepsToRollback as $step) {
            $step->compensate($data);
            $this->log(class_basename($step), 'compensate', 'success', $data);
        }
    }

    protected function log($step, $action, $status, $payload)
    {
        SagaLog::create([
            'trace_id' => $this->traceId,
            'step_name' => $step,
            'action' => $action,
            'status' => $status,
            'payload' => json_encode($payload)
        ]);
    }
}
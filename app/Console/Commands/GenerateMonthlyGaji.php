<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\GajiController;
use Illuminate\Http\Request;

class GenerateMonthlyGaji extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gaji:generate-monthly {--month=} {--year=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly salary for all active employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->option('month') ?: now()->month;
        $year = $this->option('year') ?: now()->year;
        
        $this->info("Generating salary for period: {$month}/{$year}");
        
        try {
            $controller = new GajiController();
            $request = new Request([
                'periode_bulan' => $month,
                'periode_tahun' => $year
            ]);
            
            $response = $controller->generateGaji($request);
            $data = json_decode($response->getContent(), true);
            
            if ($data['status'] === 'success') {
                $this->info($data['message']);
                if (!empty($data['errors'])) {
                    $this->warn('Some errors occurred:');
                    foreach ($data['errors'] as $error) {
                        $this->warn('- ' . $error);
                    }
                }
            } else {
                $this->error('Failed to generate salary: ' . $data['message']);
            }
            
        } catch (\Exception $e) {
            $this->error('Error generating salary: ' . $e->getMessage());
        }
    }
}

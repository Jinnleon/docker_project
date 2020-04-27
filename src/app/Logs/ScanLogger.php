<?php
namespace App\Logs;

use App\Exceptions\ScanDirException;
use App\GlobalValues;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;


class ScanLogger {

    /**
     * @throws ScanDirException
     */
    public static function getLogFile() {
        $log_file = dirname(__DIR__).'/Logs/scan_log.json';
        if (file_exists($log_file)) {
            $logs = file_get_contents($log_file);
            return json_decode($logs, true);
        } else {
            throw new ScanDirException('Scan log file does not exist.');
        }
    }

    /**
     * @param $source_dir
     * @param $depth
     * @param $user Authenticatable
     */
    public static function saveScanLog($source_dir, $depth, $user) {
        $scan_data = [
            GlobalValues::SCAN_DIR      => $source_dir,
            GlobalValues::DEPTH         => $depth,
            GlobalValues::USER_NAME     => $user['name'],
            GlobalValues::SCAN_DATETIME => Carbon::now()
        ];

        self::saveLogFile($scan_data);
    }

    private static function saveLogFile($scan_data) {
        $jsonData = json_encode($scan_data);
        $log_file = dirname(__DIR__).'/Logs/scan_log.json';

        if (file_exists($log_file)) {
            $oldLog     = file_get_contents($log_file);
            $decodeLogs = json_decode($oldLog, true);
            $decodeLogs['Scan_logs'][] = $scan_data;
            $jsonData   = json_encode($decodeLogs);
            file_put_contents($log_file, $jsonData);
        } else {
            file_put_contents($log_file, '{"Scan_logs":['.$jsonData.']}');
        }

        return;
    }

}

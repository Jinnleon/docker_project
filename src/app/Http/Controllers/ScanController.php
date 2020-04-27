<?php

namespace App\Http\Controllers;

use App\Exceptions\ScanDirException;
use App\GlobalValues;
use App\Http\Helpers\ErrorHelper;
use App\Http\Helpers\ResponseHelper;
use App\Http\Request\ScanDirRequest;
use App\Logs\ScanLogger;
use App\Models\FileModel;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller {

    private $files = [];
    private $start_depth;
    private $scan_dir;

    /**
     * @OA\PUT(
     *      path="/api/scanDirectory",
     *      operationId="scan_directory",
     *      tags={"Scan directory"},
     *      summary="Scanning directory with depth and save data to DB.",
     *      description="",
     *      @OA\Parameter(
     *         name="dir",
     *         in="query",
     *         description="Path to directory for scanning. Example: /var/www/html/public",
     *         required=true,
     *         example="/var/www/html/public"
     *     ),
     *     @OA\Parameter(
     *         name="depth",
     *         in="query",
     *         description="Depth for scanning.",
     *         required=false
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=200
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="ok"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string",
     *                  example="Directory: source_dir scanned successfully."
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid user credentials",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=401
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="failed"
     *              ),
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Invalid email or password."
     *              ),
     *          )
     *      ),
     *     security={{ "apiAuth": {} }}
     *  )
     * @return \Illuminate\Http\JsonResponse
     */
    public function scanDirectory(ScanDirRequest $request) {
        try {
            DB::beginTransaction();
            $source_dir        = trim($request->query->get('dir'), '"');
            $depthInRequest    = $request->query->get(GlobalValues::DEPTH);
            $depth             = isset($depthInRequest) ? (int)$depthInRequest : 0;
            $this->start_depth = $depth;
            $this->scan_dir    = $source_dir;
            $currentUser       = auth()->user();
            //check if source directory already scanned
            $isScanned = FileModel::isDirScanned($source_dir);

            if ($isScanned) {
                //remove all dir data
                FileModel::removeFilesFromDir($source_dir);
            }
            //scan source directory
            $this->scanDir($source_dir, $depth);
            //save files in data base
            FileModel::saveFilesInDb($this->files);
            //save scan logs
            ScanLogger::saveScanLog($source_dir, $depth, $currentUser);
            DB::commit();
            return ResponseHelper::sendSuccessResponse("Directory: {$source_dir} scanned successfully.");
        } catch (\Throwable $exception) {
            DB::rollBack();
            return ResponseHelper::sendErrorResponse(ErrorHelper::SCAN_DIR_ERROR, $exception->getMessage());
        }
    }

    /**
     * @OA\GET(
     *      path="/api/getScanLogs",
     *      operationId="get_scan_logs",
     *      tags={"Scan directory"},
     *      summary="Get log data about directory scanning from a log file.",
     *      description="If user send correct JWT token, will get scanning logs in json format.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=200
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="ok"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string",
     *                  example="Scanned logs data"
     *              )
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid user credentials",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=401
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="failed"
     *              ),
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Invalid email or password."
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Invalid request data",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=403
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="failed"
     *              ),
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Scan log file does not exist."
     *              ),
     *          )
     *      ),
     *     security={{ "apiAuth": {} }}
     *  )
     * @return \Illuminate\Http\JsonResponse
     */
    public function getScanLogs() {
        try {
            $log_data = ScanLogger::getLogFile();
            return ResponseHelper::sendSuccessResponse($log_data);
        } catch (\Throwable $exception) {
            return ResponseHelper::sendErrorResponse(ErrorHelper::SCAN_DIR_ERROR, $exception->getMessage());
        }
    }

    /**
     * @param $source_dir
     * @param int $directory_depth
     * @param bool $hidden
     * @throws ScanDirException
     */
    private function scanDir($source_dir, $directory_depth = 0, $hidden = FALSE) {
        if ($fp = opendir($source_dir)) {
            $new_depth  = $directory_depth - 1;
            $source_dir = rtrim($source_dir, '/').'/';

            while (FALSE !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if ( ! trim($file, '.') OR ($hidden == FALSE && $file[0] == '.')) {
                    continue;
                }

                if (($directory_depth < 1 OR $new_depth > 0) && is_dir($source_dir.$file)) {
                    $this->scanDir($source_dir.$file.'/', $new_depth, $hidden);
                } else {
                    if ((($directory_depth-1) < 1 OR $new_depth > 0) && is_file($source_dir.$file)) {
                        $fileInfo = [GlobalValues::SCAN_DIR  => $this->scan_dir,
                            GlobalValues::PATH      => $source_dir.$file,
                            GlobalValues::DEPTH     => $this->start_depth - $directory_depth,
                            GlobalValues::FILE_NAME => pathinfo($file, PATHINFO_FILENAME),
                            GlobalValues::EXTENSION => pathinfo($file, PATHINFO_EXTENSION)
                        ];

                        $this->files[] = $fileInfo;
                    }
                }
            }
            closedir($fp);
            return;
        }

        throw new ScanDirException('Can not open directory.');
    }

}

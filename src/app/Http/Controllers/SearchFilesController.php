<?php
namespace App\Http\Controllers;

use App\GlobalValues;
use App\Http\Helpers\ErrorHelper;
use App\Http\Helpers\ResponseHelper;
use App\Http\Request\SearchFilesRequest;
use App\Models\FileModel;

class SearchFilesController extends Controller {

    /**
     * @OA\GET(
     *      path="/api/searchFiles",
     *      operationId="search_files",
     *      tags={"Search files"},
     *      summary="Search files by criteria: file_name, depth, type.",
     *      description="",
     *     @OA\Parameter(
     *         name="file_name",
     *         in="query",
     *         description="File name for search.",
     *         required=false
     *     ),
     *     @OA\Parameter(
     *         name="depth",
     *         in="query",
     *         description="Depth of file(0,1,2,3)",
     *         required=false
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="File type(txt,jpg,php)",
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
     *                  example={}
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
     * @param SearchFilesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchFiles(SearchFilesRequest $request) {
        try {
            $file_name = $request->query->get(GlobalValues::FILE_NAME);
            $depth     = $request->query->get(GlobalValues::DEPTH);
            $type      = $request->query->get(GlobalValues::TYPE);

            $criteria = [
                GlobalValues::FILE_NAME => $file_name,
                GlobalValues::DEPTH     => $depth,
                GlobalValues::EXTENSION => $type,
            ];

            $files = FileModel::getFilesByCriteria($criteria);

            return ResponseHelper::sendSuccessResponse($files);
        } catch (\Throwable $exception) {
            return ResponseHelper::sendErrorResponse(ErrorHelper::SEARCH_ERROR, $exception->getMessage());
        }
    }

}

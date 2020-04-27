<?php
namespace App\Models;

use App\GlobalValues;
use Illuminate\Support\Facades\DB;

class FileModel {

    const TABLE_NAME = 'files';

    public static function saveFilesInDb($filesDataArray) {
        DB::table(self::TABLE_NAME)->insertOrIgnore(
            $filesDataArray
        );

        return;
    }

    public static function isDirScanned($source_dir) {
        $result = DB::table(self::TABLE_NAME)
                    ->where(GlobalValues::SCAN_DIR,'=' ,[$source_dir])
                    ->limit(1)
                    ->get();

        return boolval(sizeof($result));
    }

    public static function removeFilesFromDir($source_dir) {
        DB::table(self::TABLE_NAME)
            ->where(GlobalValues::SCAN_DIR,'=' ,[$source_dir])
            ->delete();

        return;
    }

    public static function getFilesByCriteria($criteria) {
        $query = DB::table(self::TABLE_NAME);

        $needResult = false;
        foreach ($criteria as $column => $value) {
            if (!is_null($value)) {
                $needResult = true;
                if ($column == GlobalValues::FILE_NAME) {
                    $query->where($column, 'LIKE', [$value.'%']);
                }
                if ($column == GlobalValues::DEPTH) {
                    $query->where($column, '=', [$value]);
                }

                if ($column == GlobalValues::EXTENSION) {
                    $query->where($column, '=', [$value]);
                }
            }
        }

        if ($needResult) {
            return $query->get();
        }

        return [];
    }

}

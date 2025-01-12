<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TokenQueryController extends BaseController
{
    public function index(Request $request)
    {
        // 获取查询参数中的令牌
        $tokenKey = $request->input('key');
        $tokenKey = substr($tokenKey, 3);
        // Default user_id
        $user_id = 1;
        // 使用 mysql_secondary 数据库连接查询 tokens 表
        $tokenInfo = DB::connection('mysql_secondary')->table('tokens')
            ->where('key', $tokenKey)
            ->where('user_id', 1)
            ->first();

        // 检查 $tokenInfo 是否为 null
        if ($tokenInfo === null) {
            // 处理 $tokenInfo 为 null 的情况
            $tokenInfo = (object) [
                'remain_quota' => 0,
                'used_quota' => 0,
            ];
        }

        // Calculate total quota
        $totalQuota = $tokenInfo->remain_quota + $tokenInfo->used_quota;

        // Fetch log information
        $logs = DB::connection('mysql_secondary')->table('logs')
            ->where('user_id', $user_id)
            ->where('token_name', $tokenInfo->name)
            ->where('created_at', '>=', now()->subDays(14))
            ->select('created_at', 'model_name', 'prompt_tokens', 'completion_tokens')
            ->orderBy('created_at', 'desc')
            ->get();

        // Convert created_at timestamp to human-readable format
        $logs->transform(function ($log) {
            // 检查 $log->created_at 是否为时间戳（long 类型）
            if (is_numeric($log->created_at)) {
                $log->created_at = date('Y-m-d H:i:s', $log->created_at); // 转换为可读的时间格式
            } else {
                $log->created_at = 'N/A'; // 如果时间戳无效，显示默认值
            }
            return $log;
        });

        // Calculate quota cost
        $logs->transform(function ($log) {
            $log->quota_cost = ($log->prompt_tokens + $log->completion_tokens) * 0.000002;
            return $log;
        });

        // 修改为使用 render 方法来渲染视图
        return $this->render('static_pages.tokenQuery', [
            'tokenInfo' => $tokenInfo,
            'totalQuota' => $totalQuota,
            'logs' => $logs,
        ]);
    }

    public function tokenQuery(Request $request)
    {
        return $this->render('static_pages.tokenQuery', [], __('dujiaoka.page-title.token_query'));
    }
}

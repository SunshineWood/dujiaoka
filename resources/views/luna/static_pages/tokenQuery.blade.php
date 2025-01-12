@extends('luna.layouts.default')

@section('content')
    <body>
    @include('luna.layouts._nav')
    <style>
        .layui-table td, .layui-table th {
            padding: 9px 5px;
        }
        .main-box {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .pay-title {
            font-size: 24px;
            font-weight: 700;
            color: #3C8CE7;
            margin-bottom: 20px;
        }
        .btn button {
            background: #3C8CE7;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn button:hover {
            background: #2a6bb1;
        }
    </style>
    <div class="main">
        <div class="layui-row">
            <div class="layui-col-md8 layui-col-md-offset2 layui-col-sm12">
                <div class="main-box">
                    <div class="pay-title">
                        <svg style="margin-bottom: -6px;" t="1603120404646" class="icon" viewBox="0 0 1024 1024"
                             version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1611" width="27" height="27">
                            <path d="M320.512 428.032h382.976v61.44H320.512zM320.512 616.448h320.512v61.44H320.512z"
                                  fill="#00EAFF" p-id="1612" data-spm-anchor-id="a313x.7781069.0.i3"
                                  class="selected"></path>
                            <path
                                d="M802.816 937.984H221.184l-40.96-40.96V126.976l40.96-40.96h346.112l26.624 10.24 137.216 117.76 98.304 79.872 15.36 31.744v571.392l-41.984 40.96z m-540.672-81.92h500.736V345.088L677.888 276.48 550.912 167.936H262.144v688.128z"
                                fill="#3C8CE7" p-id="1613" data-spm-anchor-id="a313x.7781069.0.i0" class=""></path>
                        </svg>
                        令牌查询
                    </div>
                    <div class="layui-card-body">
                        <p style="color: #3C8CE7;font-size: 18px;font-weight: 700; text-align: center;margin: 20px 0">
                            请输入要查询的令牌
                        </p>
                        <form class="layui-form" id="tokenQueryForm">
                            <div class="entry">
                                <span class="l-msg">令牌:</span>
                                <label class="input">
                                    <input type="text" name="key" required lay-verify="required"
                                           placeholder="请输入令牌" autocomplete="off">
                                </label>
                                <button class="btn" lay-submit lay-filter="tokenQuery">
                                    查询
                                </button>
                            </div>
                        </form>
                    </div>

                    @if($tokenInfo ?? false)
                        <div class="card">
                            <div class="card-body">
                                <div class="quota-info">
                                    <p class="quota-item"><strong>令牌总额:</strong> {{ ($tokenInfo->remain_quota ?? 0) + ($tokenInfo->used_quota ?? 0) }}</p>
                                    <p class="quota-item"><strong>剩余额度:</strong> {{ $tokenInfo->remain_quota ?? 0 }}</p>
                                    <p class="quota-item"><strong>使用额度:</strong> {{ $tokenInfo->used_quota ?? 0 }}</p>
                                    <p class="quota-item"><strong>有效期:</strong> 永久</p>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">调用详情</div>
                            <div class="card-body">
                                <table class="table table-full-width">
                                    <thead>
                                    <tr>
                                        <th>时间</th>
                                        <th>模型</th>
                                        <th>提示</th>
                                        <th>补全</th>
                                        <th>花费</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($logs ?? [] as $log)
                                        <tr>
                                            <td>
                                                @if(is_numeric($log->created_at))
                                                    <script>
                                                        // 将时间戳转换为可读的时间格式
                                                        document.write(new Date({{ $log->created_at }} * 1000).toLocaleString());
                                                    </script>
                                                @else
                                                    {{ $log->created_at ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td>{{ $log->model_name ?? '' }}</td>
                                            <td>{{ $log->prompt_tokens ?? 0 }}</td>
                                            <td>{{ $log->completion_tokens ?? 0 }}</td>
                                            <td>{{ $log->quota_cost ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('luna.layouts._footer')
    </body>
@endsection

@section('js')
    <script>
        layui.use(['form'], function () {
            var form = layui.form;

            form.on('submit(tokenQuery)', function (data) {
                var key = data.field.key;
                if (key) {
                    window.location.href = "{{ route('token-query-by-key') }}?key=" + key;
                }
                return false; // 阻止表单跳转
            });
        });
    </script>
@endsection

<style>
    /* 设置全局字体为微软雅黑 */
    body {
        font-family: 'Microsoft YaHei', sans-serif;
    }

    /* 令牌信息部分的样式 */
    .quota-info {
        display: flex;
        flex-direction: column;
        gap: 15px; /* 增加间距 */
    }

    .quota-item {
        margin: 0;
        font-size: 16px;
        color: #333;
    }

    /* 表格样式 */
    .table-full-width {
        width: 100%; /* 表格宽度充满页面 */
        border-collapse: separate;
        border-spacing: 0 10px; /* 行间距 */
    }

    .table-full-width th,
    .table-full-width td {
        padding: 12px 15px; /* 增加单元格内边距 */
        text-align: left;
    }

    .table-full-width th {
        background-color: #3C8CE7; /* 表头背景色 */
        color: #fff; /* 表头文字颜色 */
        font-weight: bold;
    }

    .table-full-width tbody tr {
        background-color: #f9f9f9; /* 表格行背景色 */
        transition: background-color 0.3s ease;
    }

    .table-full-width tbody tr:hover {
        background-color: #f1f1f1; /* 鼠标悬停时的背景色 */
    }

    /* 卡片样式 */
    .card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #f8f9fa;
        padding: 15px;
        font-size: 18px;
        font-weight: bold;
        border-bottom: 1px solid #e0e0e0;
    }

    .card-body {
        padding: 20px;
    }
</style>

<?php


namespace App\Controller\Admin;

use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\View\RenderInterface;
use App\Controller\BaseController;

/**
 * ##请求日志管理##
 * author: yy
 * Class RequestLog.
 */
class RequestLogController extends BaseController
{
    /**
     * ##^列表展示##
     * @return mixed
     */
    public function showList(RenderInterface $render)
    {
        if (isAjax($this->request)) {

            $requestData = $this->request -> all();
            $where = createSearchWhere($requestData);

            $data_res = Db::table('request_log')
                ->where($where)
                ->offset(getOffset($requestData))
                ->limit($requestData['limit'])
                ->get();

            $data_count = Db::table('request_log')
                ->where($where)
                ->count();

            $data['code'] = 0;
            $data['count'] = $data_count;
            $data['data'] = $data_res;

            return $this->response->json($data);
        }else{
            return $render->render('request_log/showList');
        }

    }
}
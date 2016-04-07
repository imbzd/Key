<?php
/**
 * 门禁钥匙权限业务逻辑
 * buzhidao
 * 2016-04-05
 */
namespace Admin\Controller;

use Any\Upload;
use Org\Net\Http;

class AccessController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){}

    //钥匙柜门禁配置
    public function cabinetaccess()
    {
        $this->display();
    }

    //钥匙柜门禁配置-保存
    public function cabinetaccesssave()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择派出所！');
        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '请选择钥匙柜！');

        $usernos = mRequest('usernos', false);
        if (!is_array($usernos) || empty($usernos)) $this->ajaxReturn(1, '请选择警员！');

        $data = array();
        foreach ($usernos as $userno) {
            $data[] = array(
                'departmentno' => $departmentno,
                'cabinetno'    => $cabinetno,
                'userno'       => $userno,
            );
        }
        $result = D('Access')->savecabinetuser($departmentno, $cabinetno, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //警员钥匙配置
    public function userkeyaccess()
    {
        $this->display();
    }

    //警员钥匙配置-保存
    public function userkeyaccesssave()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '请选择派出所！');
        
        $usernos = mRequest('usernos', false);
        if (!is_array($usernos) || empty($usernos)) $this->ajaxReturn(1, '请选择警员！');

        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '请选择钥匙柜！');

        $keynos = mRequest('keynos', false);
        if (!is_array($keynos) || empty($keynos)) $this->ajaxReturn(1, '请选择钥匙！');

        $data = array();
        foreach ($usernos as $userno) {
            foreach ($keynos as $keyno) {
                $data[] = array(
                    'departmentno' => $departmentno,
                    'userno'       => $userno,
                    'cabinetno'    => $cabinetno,
                    'keyno'        => $keyno,
                );
            }
        }
        $result = D('Access')->saveUserkeyaccess($departmentno, $usernos, $cabinetno, $data);
        if ($result) {
            $this->ajaxReturn(0, '保存成功！');
        } else {
            $this->ajaxReturn(1, '保存失败！');
        }
    }

    //【下发】门禁权限
    public function sendCabinetAccess()
    {
        $departmentno = $this->_getDepartmentno();
        if (!$departmentno) $this->ajaxReturn(1, '未知派出所！');

        $cabinetno = $this->_getCabinetno();
        if (!$cabinetno) $this->ajaxReturn(1, '未知钥匙柜！');

        //获取部门关联的监控软件
        $mtserverinfo = M('department_monitorserver')->alias('a')
                      ->field('a.*, b.mtserverip, b.mtserverport, b.online, b.lastheartbeattime')
                      ->join(' __MONITORSERVER__ b on a.mtserverid=b.mtserverid')
                      ->where(array('departmentno'=>$departmentno))
                      ->find();
        if (!is_array($mtserverinfo) || empty($mtserverinfo)) $this->ajaxReturn(1, '没有关联监控软件！');

        $departmentno = dechex($departmentno);
        if (strlen($departmentno)==1) $departmentno = '0'.$departmentno;

        $cabinetno = dechex($cabinetno);
        if (strlen($cabinetno)==1) $cabinetno = '0'.$cabinetno;
        
        $data = 'AA BB 00 05 55 '.$departmentno.' '.$cabinetno.' 00 00 00 EE FF';
        $result = $this->_socketTcpSend($mtserverinfo['mtserverip'], $mtserverinfo['mtserverport'], $data);
        if ($result) {
            $this->ajaxReturn(0, '下发门禁权限成功！');
        } else {
            $this->ajaxReturn(1, '下发门禁权限失败！');
        }
    }
}
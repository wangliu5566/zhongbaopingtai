<?php
/**
 * Created by PhpStorm.
 * User: Angoo
 * Date: 2017/6/23
 * Time: 15:04
 */

namespace api\modules\v1\admin\controllers;


use api\controllers\BaseAPIAuthController;
use api\models\AuthAssignment;
use api\models\AuthItem;
use api\models\AuthItemChild;
use api\models\SiderBar;
use common\helpers\Helper;


use dosamigos\qrcode\QrCode;
use yii\base\Widget;
use yii\helpers\Json;
use yii\rbac\Role;



class PermissionController extends BaseAPIAuthController
{
    /*权限增加*/
    public function actionAddPermission()
    {
        /*获取get数据*/
        $item = \Yii::$app->request->get('item');
        $description = \Yii::$app->request->get('description');
        $this->actionCreatePermission($item, $description);
        return Helper::formatJson('200','添加权限成功','');
    }

    /*创建权限*/
    public function actionCreatePermission($item, $description)
    {
        $auth = \Yii::$app->authManager;
        $createPost = $auth->createPermission($item);
        $createPost->description = $description;
        $res = $auth->add($createPost);

    }

    /*权限删除*/
    public function actionDeletePermission($item)
    {
        $count = AuthItem::deleteAll('name=:name',[':name'=>$item]);
        if($count>0){
            echo  [
              'data'=>'',
              'status'=>1,
                'msg'=>'删除成功'
            ];
            return;
        }else{
            echo  json_encode([
                'data'=>'',
                'status'=>0,
                'msg'=>'删除失败'
            ]);
            return;
        }
    }

    /*角色增加*/
    public function actionAddRole()
    {
        /*获取get数据*/
        $item = \Yii::$app->request->get('item');
        $this->createRole($item);
        return Helper::formatJson('200','角色添加成功','');
    }



    /*创建角色*/
    public function createRole($item,$description=false)
    {
        $auth = \Yii::$app->authManager;
        $role = $auth->createRole($item);

        if($description == false){
            $role->description = '创建了 ' . $item . ' 角色';
        }else{
            $role->description = $description;
        }

        $res = $auth->add($role);
        if($res){
            return true;
        }else{
            return Helper::formatJson(1006,'角色存在','');
        }

    }

    /*给角色分配许可*/
    public static function actionCreateEmpowerment()
    {
        $items = \Yii::$app->request->post();
        $authItemChild = new AuthItemChild();
        $checkResult = $authItemChild->checkIssetRoleAndPermission($items);

        if($checkResult === false){
           return Helper::formatJson(1004,'角色或者权限不存在','');
        }
        $auth = \Yii::$app->authManager;
        $parent = $auth->createRole($items['role']);
        $child = $auth->createPermission($items['permission']);
        $res = $auth->addChild($parent, $child);
        if(!$res){
           return Helper::formatJson(1004,'角色或者权限不存在','');
        }else{
            return Helper::formatJson(200,'添加成功','');
        }
    }

    /*给角色分配用户*/
    public static  function actionAssign()
    {
        $item = \Yii::$app->request->post();
        $auth = \Yii::$app->authManager;
        $reader = $auth->createRole($item['role']);
        $res = $auth->assign($reader, $item['userId']);
        if($res){
            return Helper::formatJson(200,'添加成功','');
        }else{
            return Helper::formatJson(1002,'添加失败','');
        }
    }

    public function actionGetAllPermissionByUserId()
    {
        $userId = \Yii::$app->request->post('userId');
        if(!isset($userId)){
            return Helper::formatJson(1001, '用户id不存在');
        }
        /*通过用户id找到相关角色*/
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermissionsByUser($userId);
        return $permission;
    }

    /*
     * 生成两种角色,目前只有两种,任务管理员和超级管理员
     */
    public function actionCreateRo()
    {
        $authManager = \Yii::$app->authManager;
        $superAdmin = $authManager->getRole('超级管理员');
        $taskAdmin = $authManager->getRole('任务管理员');
        $authManager->addChild($superAdmin, $taskAdmin);

    }

    /**
     * 获取所有权限
     * @return [type] [description]
     */
    public function actionGetAllPermission()
    {
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermissions();

        return Helper::formatJson(200, 'Ok', array_values($permission));
    }

    /**
     * 获取角色权限列表
     * @return array
     */
    public function actionGetPermissionAndRoleList(){
        $auth = \Yii::$app->authManager;
        $order = \Yii::$app->request->post('order') ?? '';
        $roleName = \Yii::$app->request->post('role') ?? '';
        $authItem = AuthItem::find();
        if(empty($order)){
            $desc = 'DESC';
        }else{
            $desc = 'ACS';
        }
        if(isset($roleName)){
            $authItem = $authItem->where(['like','name',$roleName]);
        }
        $roles = $authItem->andWhere(['type'=>1])->asArray()->all();


            $permission = [];
            foreach($roles as $key=>$ro){
                $data = $auth->getPermissionsByRole($ro['name']);

                $stuList = json_decode(Json::encode($data),true);
                $stuList = array_column($stuList,'description');
                $stuList = implode(',',$stuList);
                $permission[$key]['permission'] = $stuList;
                $permission[$key]['role'] = $ro['name'];
                $permission[$key]['created_at'] = date('Y-m-d',$ro['created_at']);
                $permission[$key]['updated_at'] = date('Y-m-d',$ro['updated_at']);
            }



        return Helper::formatJson(200,'',$permission);
    }

    /**
     * 通过角色查看权限
     * @return array
     */
    public function actionModifyRolesPermission(){

        $auth = \Yii::$app->authManager;
        //更加角色获得角色数据
        $role = \Yii::$app->request->post('role') ?? '';
        if(empty($role)){
            return Helper::formatJson(1001);
        }
        $roleData = $auth->getRole($role);
        $data = $auth->getPermissionsByRole($role);
        $stuList = json_decode(Json::encode($data),true);
        $stuList = array_column($stuList,'name');

        return Helper::formatJson(200,'',['role'=>$roleData->name,'permission'=>$stuList,'description'=>$roleData->description]);
    }

    /**
     * 获取所有角色
     * @return array
     */
    public function actionGetAllRoles()
    {
        $auth = \Yii::$app->authManager;
        $role = $auth->getRoles();

        return Helper::formatJson(200,'', array_values($role));
    }

    /**
     * @return array
     */
    public function actionAddPermissionsForRole(){
        if(\Yii::$app->request->isPost){

            $permissions = \Yii::$app->request->post('permission') ?? '';
            $secondPermission = \Yii::$app->request->post('secondPermission') ?? '';
            $role = \Yii::$app->request->post('role') ?? '';
            $description = \Yii::$app->request->post('description')?? '';



            if(empty($role) || empty($description)  ){
                return Helper::formatJson(1001);
            }
            $auth = \Yii::$app->authManager;
            //新增角色,先检查角色是否存在
           $roleRes =  $auth->getRole($role);

            if(!is_object($roleRes)){

                $this->createRole($role,$description);
            }

            if(is_string($permissions) && is_string($secondPermission)){
                $permissions = explode(',',$permissions);
                $secondPermission = explode(',',$secondPermission);
                $parent = $auth->createRole($role);

               // $transaction = \Yii::$app->db->beginTransaction();
                //删除角色所有权限
                 $res = AuthItemChild::deleteAll(['parent'=>$role]);

                //通过二级权限找到一级权限
                $barData = BAR;
                $firstPermission = [];
                $firstPermission = [];
                foreach ($secondPermission as $key=>$se){
                    if(isset($barData[$se])){
                        $firstPermission[$key] = BAR[$se];
                    }
                }
                array_unique($firstPermission);
                //把一级,二级,三级数组相加
                $permissions = array_unique(array_merge($permissions,$secondPermission,$firstPermission));

                foreach($permissions as $pe){

                    $child = $auth->createPermission($pe);
                    $res = $auth->addChild($parent, $child);
                }

                    return Helper::formatJson(200);

            }else{
                return Helper::formatJson(1001);
            }
        }else{
            return Helper::formatJson(1003);
        }
    }

    /**
     * 获取侧边栏数据
     */
    public function actionGetSiderBar(){
        if(\Yii::$app->request->isPost){
            $userId = \Yii::$app->request->post('userId') ?? '';
            if(empty($userId)){
                return Helper::formatJson(1001,'缺少数据');
            }
           $role = AuthAssignment::find()->where(['user_id'=>intval($userId)])->asArray()->one();

            if(!isset($role['item_name'])){
                return Helper::formatJson(1001,'缺少数据,用户没有分配角色');
            }

            $auth = \Yii::$app->authManager;
           // $rolePermission = $auth->getPermissionsByUser($role['item_name']);
            $rolePermission = AuthItemChild::find()->where(['parent'=>$role['item_name']])->asArray()->all();

            $rolePermission = array_column($rolePermission,'child','child');
            $firstData = [];
            $secondData = [];
            foreach (SIDER as $key1=>$sis){
                if(isset($rolePermission[$sis['value']])){
                    $firstData[] = $sis['title'];
                }
                foreach ($sis['children'] as $si){
                    if(isset($rolePermission[$si['value']])){
                        $secondData[] = $si['title'];
                    }
                }
            }

           return Helper::formatJson(200,'', ['first'=>$firstData,'second'=>$secondData]);
        }else{
            return Helper::formatJson(1003);
        }
    }

    /**
     * 新增时权限的分级显示
     * @return array
     */
    public function actionPermissionList(){
        $role = \Yii::$app->request->post('role') ?? '';
        $auth = \Yii::$app->authManager;
        $data = SIDER;
        if(empty($role)){
            $rolePermission = [];
            $roleName= '';
            $description = '';
        }else{
            $rolePermission = AuthItemChild::find()->where(['parent'=>$role])->asArray()->all();
            $rolePermission = array_column($rolePermission,'child','child');
            $roleData = $auth->getRole($role);
            $roleName = $roleData->name;
            $description = $roleData->description;

        }
        //var_dump($rolePermission);exit;

        //获取系统管理权限
        foreach (SIDER as $key1=>$sis){

            //判断一级分类是否为打钩
            $firstChecked = 0;
          foreach($sis['children'] as $key2=>$si){

              $child = $auth->getChildren($si['value']);
              $si = 0;
              $da = [];
              //判断二级分类权限是否为全选
              $secondCheckAll = 0;
              foreach ($child as $key3=>$ch){
                  $si++;

                  $da[$si]['title'] =  $ch->description;
                  $da[$si]['value'] =  $ch->name;
                  if(isset($rolePermission[$ch->name])){
                      $da[$si]['checked'] =  true;
                      $secondCheckAll ++;
                  }else{
                      $da[$si]['checked'] =  false;
                  }

              }
              //如果为全选,那么二级为打钩状态
              if($secondCheckAll == count($child)){
                    $data[$key1]['children'][$key2]['checked'] = true;
                    $firstChecked ++ ;
              }
              $data[$key1]['children'][$key2]['children'] = array_values($da);
          }
            //如果为全选,那么一级为打钩状态
            if($firstChecked == count($sis['children'])){
                $data[$key1]['checked'] = true;
            }
        }
        return Helper::formatJson(200,'',['role'=>$roleName,'permission'=>$data,'description'=>$description]);
    }

    /**
     * @return array
     */
    public function actionDeleteRole(){
        $role = \Yii::$app->request->post('role');
        $auth = \Yii::$app->authManager;
        $roleObj = $auth->getRole($role);
        $auth->remove($roleObj);
        return Helper::formatJson(200);
    }

    public function actionQrCode($detail){
        $errorCorrectionLevel = 'L';  // 纠错级别：L、M、Q、H
        $matrixPointSize = 1.5; // 点的大小：1到10

        QrCode::png($detail,'123.png',$errorCorrectionLevel,$matrixPointSize,2);


    }

}
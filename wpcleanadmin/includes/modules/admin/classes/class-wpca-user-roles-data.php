<?php
/**
 * WPCleanAdmin User Roles Data
 *
 * 承载角色默认能力清单与角色 CRUD 操作，从 User_Roles 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wpca-user-roles-data-capabilities-tasks.php';
require_once __DIR__ . '/class-wpca-user-roles-data-crud-tasks.php';

/**
 * 用户角色数据/操作类
 */
class User_Roles_Data {

    use UserRolesDataCapabilitiesTasks;
    use UserRolesDataCrudTasks;
}

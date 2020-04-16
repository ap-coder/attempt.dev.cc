<?php

namespace SOFTWARE;

class FileModel extends Model
{

    public function getByFilename($file)
    {

        global $wpdb;

        $user_id = get_current_user_id();

        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}software WHERE filename = %s AND user_id = %d", $file, $user_id));

        return $result;

    }

    public function retrieveFile($id)
    {

        global $wpdb;

        $user_id = get_current_user_id();

        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}software WHERE id = %d", $id));

        return $result;

    }

    public function getById($id)
    {

        global $wpdb;

        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}software WHERE id = %d", $id));

        return $result;

    }

    public function getAllFiles()
    {

        global $wpdb;

        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}software WHERE 1=1");

        return $results;

    }

    public function getByUserId($user_id)
    {

        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}software WHERE `user_id` = %d", $user_id));

        return $results;

    }

    public function softwareSave($data)
    {

        global $wpdb;

        $result = $wpdb->insert($wpdb->prefix . 'software', $data, array(

            '%s', '%s', '%s','%s',

        ));

        if ($result) {

            return $result;

        } else {

            return $wpdb->last_error;

        }

    }

    public function save($data)
    {

        global $wpdb;

        $prepare = array_pad(array('%s'), count($data), '%s');

        /* $prepare = array(

        '%s','%s','%s','%s','%s','%s','%s'

        ); */
        $keys = array_keys($data);
        $keys = implode('`,`', $keys);
        $values = array_values($data);
        $values = implode('","', $values);

        $wpdb->query("INSERT INTO {$wpdb->prefix}software (`" . $keys . "`) VALUES (\"" . $values . "\")");

        if (empty($wpdb->last_error)) {

            return $wpdb->insert_id;

        } else {

            return $wpdb->last_error;

        }

    }

    public function delete($file_id)
    {

        global $wpdb;

        $result = $wpdb->delete($wpdb->prefix . 'software', array('id' => $file_id), array('%d'));

        if ($result) {

            return true;

        } else {

            return $wpdb->last_error;

        }

    }

}

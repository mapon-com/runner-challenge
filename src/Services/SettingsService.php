<?php


namespace App\Services;


use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

class SettingsService
{
    public function get(string $name, $default = null)
    {
        $bean = $this->getBean($name);
        if (!$bean) {
            return $default;
        }
        return json_decode($bean->value, true);
    }

    public function set(string $name, $value)
    {
        $bean = $this->getBean($name);
        if (!$bean) {
            $bean = R::dispense('settings');
        }

        $bean->name = $name;
        $bean->value = json_encode($value);

        R::store($bean);

        return true;
    }

    /**
     * @param string $name
     * @return NULL|OODBBean|\stdClass
     */
    private function getBean($name): ?OODBBean
    {
        return $bean = R::findOne('settings', 'name = ?', [$name]);
    }
}
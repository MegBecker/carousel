<?php

namespace carousel;

/**
 *
 * @author Matthew McNaney <mcnaney at gmail dot com>
 * @license http://opensource.org/licenses/lgpl-3.0.html
 */
class Module extends \Module implements \SettingDefaults {

    public function __construct()
    {
        parent::__construct();
        $this->setTitle('carousel');
        $this->setProperName('Carousel for Bootstrap themes');
    }

    public function getController(\Request $request)
    {
        $cmd = $request->shiftCommand();
        if ($cmd == 'admin' && \Current_User::allow('carousel')) {
            $admin = new \carousel\Controller\Admin($this);
            return $admin;
        }
    }

    public function runTime(\Request $request)
    {
        if (!$request->isVar('module')) {
            $display = \carousel\SlideFactory::display();
            if (!empty($display)) {
                \Layout::add($display, 'carousel', 'slides');
            }
        }
    }

    public function afterRun(\Request $request, \Response $response)
    {
        $key = \Key::getCurrent();
        if ($key && !$key->isDummy()) {
            $this->checkKey($key->id);
        }
    }

    private function checkKey($key_id)
    {
        javascript('jquery');
        \Layout::addJSHeader('<script type="text/javascript" src="' . PHPWS_SOURCE_HTTP
                . 'mod/carousel/javascript/add_slide.js"></script>');
        $db = \Database::newDB();
        $t = $db->addTable('caro_keyed_slide');
        $t->addField('slide_id');
        $t->addFieldConditional('key_id', $key_id);
        $result = $db->selectColumn();
        if (\Current_User::allow('carousel')) {
            $this->miniAdmin($result, $key_id);
        }
        if (!empty($result)) {
            \carousel\SlideFactory::showKeySlide($result);
        }
    }

    private function miniAdmin($result, $key_id)
    {
        if (empty($result)) {
            $db = \Database::newDB();
            $t2 = $db->addTable('caro_slide');
            $t2->addOrderBy('title');
            $t2->addField('title');
            $t2->addField('id');
            $slides = $db->select();
            if (empty($slides)) {
                return;
            }

            $opt[] = '<option id="0" style="">' . t('Add slide to this page') . '</option>';
            foreach ($slides as $s) {
                $opt[] = '<option value="' . $s['id'] . '">' . substr($s['title'],
                                0, 15) . '</option>';
            }

            $select = '<select data-key-id="' . $key_id . '" id="add-slide" style="font-size:12px" class="form-control">'
                    . implode("\n", $opt) . '</select>';
        } else {
            $select = '<a href="javascript:void(0)" id="remove-slide" data-key-id="' . $key_id . '">' . t('Remove slide from page') . '</a>';
        }
        \MiniAdmin::add('carousel', $select);
    }

    public function getSettingDefaults()
    {
        $s['min_width'] = 1000;
        $s['min_height'] = 100;
        $s['iteration'] = 0;
        $s['time_interval'] = 5;
        $s['display_mobile'] = false;
        // transition 0 slide, 1 fade
        $s['transition'] = 0;
        $s['indicator'] = 0;
        return $s;
    }

}

?>

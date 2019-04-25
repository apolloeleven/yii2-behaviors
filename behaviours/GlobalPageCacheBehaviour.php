<?php

namespace apollo11\yii2GlobalBehaviours\behaviours;

use apollo11\yii2GlobalBehaviours\exceptions\GlobalPageCacheException;
use yii\base\Behavior;
use yii\base\Controller;
use Yii;

/**
 * Class GlobalCacheBehavior
 * @package common\behaviors
 */
class GlobalPageCacheBehaviour extends Behavior
{

    const VARIATION_BY_LANGUAGE = 'variation_by_language';
    const VARIATION_BY_URL = 'variation_by_url';

    /**
     * @var array
     */
    public $rules = [];

    /**
     * @var array
     */
    public $variations = [];

    /**
     * @var integer
     */
    public $duration = 60;

    /**
     * @return array
     * @author Saiat Kalbiev <kalbievich11@gmail.com>
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction'
        ];
    }

    /**
     * @author Saiat Kalbiev <kalbievich11@gmail.com>
     * @throws GlobalPageCacheException
     */
    public function beforeAction()
    {
        if (!$this->rules) {
            throw new GlobalPageCacheException('Invalid rules provided');
        }

        $currentControllerId = Yii::$app->controller->id;
        $currentActionId = Yii::$app->controller->action->id;

        foreach ($this->rules as $rule) {

            if (!isset($rule['controller']) || !$rule['controller']) {
                throw new GlobalPageCacheException('Invalid rule. Controller id not provided.');
            }

            if ($rule['controller'] == $currentControllerId) {

                if (isset($rule['duration']) && $rule['duration']) {
                    $this->duration = $rule['duration'];
                }

                if (!isset($rule['actions']) || !$rule['actions']) {
                    throw new GlobalPageCacheException('Invalid rule. Action id(s) not provided.');
                }

                if (isset($rule['except'])) {

                    if (!is_array($rule['except'])) {
                        throw new GlobalPageCacheException('Invalid rule. Except parameter should be of type array.');
                    }

                    if (in_array($currentActionId, $rule['except'])) {
                        return;
                    }
                }

                if (in_array('*', $rule['actions']) || in_array($currentActionId, $rule['actions'])) {
                    $this->attachBehavior($rule);
                }
            }
        }
    }

    /**
     * @param $rule array
     * @return array
     * @author Saiat Kalbiev <kalbievich11@gmail.com>
     * @throws GlobalPageCacheException
     */
    private function generateVariations($rule)
    {
        $variations = [];

        if (isset($rule['variations'])) {

            if (!is_array($rule['variations'])) {
                throw new GlobalPageCacheException('Invalid rule. Variations parameter should be of type array.');
            }

            foreach ($rule['variations'] as $variation) {
                if ($variation == self::VARIATION_BY_LANGUAGE) {
                    $variations[] = \Yii::$app->language;
                } else if ($variation == self::VARIATION_BY_URL) {
                    $variations[] = Yii::$app->request->absoluteUrl;
                }
            }
        }

        return $variations;
    }

    /**
     * @param $rule array
     * @author Saiat Kalbiev <kalbievich11@gmail.com>
     * @throws GlobalPageCacheException
     */
    private function attachBehavior($rule)
    {
        $variations = $this->generateVariations($rule);

        Yii::$app->controller->attachBehavior('pageCacheBehaviour', [
            'class' => 'yii\filters\PageCache',
            'duration' => $this->duration,
            'variations' => $variations
        ]);
    }
}

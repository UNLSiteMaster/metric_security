<?php
namespace SiteMaster\Plugins\Metric_security;

use HtmlValidator\Validator;
use SiteMaster\Core\Auditor\Logger\Metrics;
use SiteMaster\Core\Auditor\MetricInterface;
use SiteMaster\Core\Config;
use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\Auditor\Scan;
use SiteMaster\Core\Auditor\Site\Page;
use SiteMaster\Core\RuntimeException;

class Metric extends MetricInterface
{
    /**
     * @param string $plugin_name
     * @param array $options
     */
    public function __construct($plugin_name, array $options = array())
    {
        $options = array_replace_recursive([
            'help_text' => [],
            'mark_machine_name_is_error' => ['title_exists', 'headings_exist'],
        ], $options);

        parent::__construct($plugin_name, $options);
    }

    /**
     * Get the human readable name of this metric
     *
     * @return string The human readable name of the metric
     */
    public function getName()
    {
        return 'Security';
    }

    /**
     * Get the Machine name of this metric
     *
     * This is what defines this metric in the database
     *
     * @return string The unique string name of this metric
     */
    public function getMachineName()
    {
        return 'metric_security';
    }

    /**
     * Determine if this metric should be graded as pass-fail
     *
     * @return bool True if pass-fail, False if normally graded
     */
    public function isPassFail()
    {
        if (isset($this->options['pass_fail']) && $this->options['pass_fail'] == true) {
            //Simulate a pass/fail metric grade
            return true;
        }

        return false;
    }

    /**
     * Scan a given URI and apply all marks to it.
     *
     * All that this
     *
     * @param string $uri The uri to scan
     * @param \DOMXPath $xpath The xpath of the uri
     * @param int $depth The current depth of the scan
     * @param \SiteMaster\Core\Auditor\Site\Page $page The current page to scan
     * @param \SiteMaster\Core\Auditor\Logger\Metrics $context The logger class which calls this method, you can access the spider, page, and scan from this
     * @throws \Exception
     * @return bool True if there was a successful scan, false if not.  If false, the metric will be graded as incomplete
     */
    public function scan($uri, \DOMXPath $xpath, $depth, Page $page, Metrics $context)
    {
        if (!$results = $this->run($uri)) {
            throw new RuntimeException('headless results are required for the seo metric');
        }
        
        foreach ($results as $machine_name=>$details) {
            $machine_name = 'security_'.$machine_name.'_'.(($details['fail'])? 'fail': 'pass');

            $point_deduction = 0;
            if (in_array($machine_name, $this->options['mark_machine_name_is_error'])) {
                //These are real errors instead of notices that can be suppressed
                $point_deduction = 1;
            }

            if (!$details['fail']) {
                $point_deduction = -1;
            }

            $mark = $this->getMark($machine_name, $details['name'], $point_deduction, $details['description']);

            $value_found = null;
            
            if (count($details['data']) == 1) {
                $value_found = $details['data'][0];
            } else if (count($details['data']) > 1) {
                $value_found = '';
                foreach ($details['data'] as $value) {
                    $value_found .= '* ' . $value . PHP_EOL;
                }

                $value_found = trim($value_found);
            }
            
            $page->addMark($mark, array(
                'value_found' => $value_found,
            ));
        }

        return true;
    }
    
    public function run($url) {
        $command = 'timeout ' . escapeshellarg(Config::get('HEADLESS_TIMEOUT')) //Prevent excessively long runs
            . ' ' . Config::get('PATH_NODE')
            . ' ' . __DIR__.'/../check.js'
            . ' ' . escapeshellarg($url)
            . ' ' . escapeshellarg(Config::get('USER_AGENT'));

        $result = shell_exec($command);

        if (!$result) {
            return false;
        }
        
        return json_decode($result, true);
    }


    /**
     * Get the help text for a mark by machine_name
     *
     * @param string $machine_name
     * @return null|string
     */
    public function getHelpText($machine_name)
    {
        if (isset($this->options['help_text'][$machine_name])) {
            return $this->options['help_text'][$machine_name];
        }

        return null;
    }
}

<?php
namespace Workana\AsyncJobs\Dispatcher;

use Bernard\QueueFactory;
use Workana\AsyncJobs\Job;
use Workana\AsyncJobs\JobManager;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class QueueDecider
{
    /**
     * @var DispatchingRuleSet
     */
    protected $rules;

    /**
     * @var string
     */
    protected $defaultQueueName;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * @param QueueFactory $queueFactory
     * @param string $defaultQueueName
     * @param Callable[]|array $rules 
     */
    public function __construct(QueueFactory $queueFactory, $defaultQueueName, array $rules = [])
    {
        $this->queueFactory = $queueFactory;
        $this->defaultQueueName = $defaultQueueName;
        $this->rules = new DispatchingRuleSet();

        $this->addRule(function(Job $job) {
            if ($job->hasPreferredQueue()) {
                return $job->getPreferredQueueName();
            }
        }, DispatchingRule::PRIORITY_HIGH);

        $this->addRules($rules);
    }

    /**
     * Decides queue
     *
     * @param Job $job
     *
     * @return \Bernard\Queue
     */
    public function decide(Job $job)
    {
        $queueName = null;
        foreach (clone $this->rules as $currentRule) {
            $queueName = $currentRule($job);

            if (!empty($queueName)) {
                break;
            }
        }

        $queueName = !empty($queueName) ? $queueName : $this->defaultQueueName;

        return $this->queueFactory->create($queueName);
    }

    /**
     * Add dispatching rule
     *
     * @param Callable $rule Rule
     * @param int $priority
     *
     * @return void
     */
    public function addRule(Callable $rule, $priority = DispatchingRule::PRIORITY_NORMAL)
    {
        if ($rule instanceof DispatchingRule) {
            $priority = $rule->getPriority();
        }

        $this->rules->insert($rule, (int) $priority);
    }

    /**
     * Add multiple rules
     *
     * @param Callable[]|array $rules
     */
    public function addRules(array $rules)
    {
        foreach ($rules as $rule) {
            if (is_array($rule)) {
                list($rule, $priority) = $rule;
                $this->addRule($rule, $priority);
            } else {
                $this->addRule($rule);
            }
        }
    }
}
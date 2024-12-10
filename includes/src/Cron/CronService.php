<?php

declare(strict_types=1);

namespace JTL\Cron;

use JTL\Abstracts\AbstractService;

/**
 * Class CronService
 * @package JTL\Cron
 */
class CronService extends AbstractService
{
    /**
     * @param CronRepository  $repository
     * @param JobQueueService $jobQueueService
     */
    public function __construct(
        protected CronRepository $repository = new CronRepository(),
        protected JobQueueService $jobQueueService = new JobQueueService()
    ) {
    }

    /**
     * @return CronRepository
     */
    public function getRepository(): CronRepository
    {
        return $this->repository;
    }

    /**
     * @return JobQueueService
     */
    public function getJobQueueService(): JobQueueService
    {
        return $this->jobQueueService;
    }

    /**
     * @return string[]
     */
    public static function getPermanentJobTypes(): array
    {
        return [
            Type::LICENSE_CHECK,
            Type::MAILQUEUE,
        ];
    }

    /**
     * @param int[] $cronIDs
     * @return bool
     */
    public function delete(array $cronIDs): bool
    {
        $this->getRepository()->deleteCron($cronIDs, self::getPermanentJobTypes());

        return $this->getJobQueueService()->delete($cronIDs);
    }
}

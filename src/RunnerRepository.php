<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Read/write access to the `runner` table.
 *
 * Runners represent RabbitMQ consumer workers that process plugin analysis
 * jobs. The active flag is read by each worker to decide whether to process
 * incoming messages.
 */
class RunnerRepository
{
    public function __construct(private readonly \mysqli $db)
    {
    }

    /**
     * Returns all runners in display order.
     *
     * Runners with an explicit sort_order (> 0) come first, ascending.
     * Runners with sort_order = 0 follow, sorted alphabetically by name.
     *
     * @return list<array<string, mixed>>
     */
    public function findAll(): array
    {
        $result = $this->db->query(
            'SELECT runner_id, runner_name, runner_slug, runner_queue,
                    runner_is_active, runner_sort_order, created_at
             FROM `runner`
             ORDER BY (runner_sort_order = 0) ASC,
                      runner_sort_order ASC,
                      runner_name ASC'
        );

        $rows = [];

        if ($result instanceof \mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Saves a display-order value for multiple runners in one transaction.
     *
     * @param array<int, int> $orders runner_id => sort_order (0 = unordered/last)
     */
    public function setOrders(array $orders): void
    {
        if (empty($orders)) {
            return;
        }

        $stmt = $this->db->prepare(
            'UPDATE `runner` SET runner_sort_order = ? WHERE runner_id = ?'
        );

        foreach ($orders as $runnerId => $order) {
            $runnerId = (int) $runnerId;
            $order    = max(0, (int) $order);
            $stmt->bind_param('ii', $order, $runnerId);
            $stmt->execute();
        }

        $stmt->close();
    }

    /**
     * Sets the active flag for a runner.
     */
    public function setActive(int $runnerId, bool $active): void
    {
        $val  = $active ? 1 : 0;
        $stmt = $this->db->prepare(
            'UPDATE `runner` SET runner_is_active = ? WHERE runner_id = ?'
        );
        $stmt->bind_param('ii', $val, $runnerId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Inserts a new runner and returns its ID.
     *
     * @throws \RuntimeException On slug collision or other DB error.
     */
    public function create(string $name, string $slug, string $queue): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO `runner` (runner_name, runner_slug, runner_queue)
             VALUES (?, ?, ?)'
        );
        $stmt->bind_param('sss', $name, $slug, $queue);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new \RuntimeException('Could not create runner: ' . $err);
        }

        $id = (int) $this->db->insert_id;
        $stmt->close();

        return $id;
    }

    /**
     * Deletes a runner by ID.
     */
    public function delete(int $runnerId): void
    {
        $stmt = $this->db->prepare('DELETE FROM `runner` WHERE runner_id = ?');
        $stmt->bind_param('i', $runnerId);
        $stmt->execute();
        $stmt->close();
    }
}

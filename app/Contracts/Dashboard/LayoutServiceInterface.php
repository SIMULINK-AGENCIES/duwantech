<?php

namespace App\Contracts\Dashboard;

interface LayoutServiceInterface
{
    /**
     * Get user's dashboard layout configuration
     *
     * @param int|null $userId
     * @return array
     */
    public function getUserLayout(?int $userId = null): array;

    /**
     * Save user's dashboard layout
     *
     * @param array $layout
     * @param int|null $userId
     * @return bool
     */
    public function saveUserLayout(array $layout, ?int $userId = null): bool;

    /**
     * Get default dashboard layout
     *
     * @return array
     */
    public function getDefaultLayout(): array;

    /**
     * Reset user layout to default
     *
     * @param int|null $userId
     * @return bool
     */
    public function resetToDefault(?int $userId = null): bool;

    /**
     * Get available layout templates
     *
     * @return array
     */
    public function getTemplates(): array;

    /**
     * Get layout template by ID
     *
     * @param string $templateId
     * @return array|null
     */
    public function getTemplate(string $templateId): ?array;

    /**
     * Apply template to user layout
     *
     * @param string $templateId
     * @param int|null $userId
     * @return bool
     */
    public function applyTemplate(string $templateId, ?int $userId = null): bool;

    /**
     * Validate layout structure
     *
     * @param array $layout
     * @return bool
     */
    public function validateLayout(array $layout): bool;

    /**
     * Clear user layout cache
     *
     * @param int|null $userId
     * @return void
     */
    public function clearUserCache(?int $userId = null): void;
}

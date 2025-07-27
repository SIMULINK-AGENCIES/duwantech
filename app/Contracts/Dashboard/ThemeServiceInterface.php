<?php

namespace App\Contracts\Dashboard;

interface ThemeServiceInterface
{
    /**
     * Get user's current theme
     */
    public function getUserTheme(int $userId): array;
    
    /**
     * Save user's theme preferences
     */
    public function saveUserTheme(int $userId, string $theme, array $customColors = []): bool;
    
    /**
     * Get all available themes
     */
    public function getAvailableThemes(): array;
    
    /**
     * Get theme configuration by name
     */
    public function getTheme(string $name): ?array;
    
    /**
     * Generate CSS variables for theme
     */
    public function generateThemeCSS(string $theme, array $customColors = []): string;
    
    /**
     * Validate theme configuration
     */
    public function validateTheme(string $theme, array $customColors = []): array;
    
    /**
     * Get default theme for new users
     */
    public function getDefaultTheme(): array;
    
    /**
     * Clear theme cache for user
     */
    public function clearUserThemeCache(int $userId): void;
    
    /**
     * Get theme usage statistics
     */
    public function getThemeStatistics(): array;
    
    /**
     * Export user theme as JSON
     */
    public function exportUserTheme(int $userId): array;
    
    /**
     * Import theme configuration for user
     */
    public function importUserTheme(int $userId, array $themeData): bool;
}

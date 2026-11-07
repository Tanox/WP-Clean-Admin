# 【已废弃 / DEPRECATED】批量更新 WP Clean Admin 插件文件版本号
#
# 本脚本硬编码 1.8.0 -> 1.8.1，且只更新 @version 注释，不更新插件头 Version、
# WPCA_VERSION、readme.txt Stable tag 等版本源，执行后会造成版本不一致甚至回退。
# 版本更新请按 CONTRIBUTING.md §9.2 的同步清单执行。
# 为防止误执行，脚本已加 fail-fast 保护。

Write-Error "DEPRECATED: 请勿使用本脚本。版本更新流程见 CONTRIBUTING.md §9.2 同步清单。"
exit 1

$pluginDir = Join-Path $PSScriptRoot "wpcleanadmin"
$oldVersion = "1.8.0"
$newVersion = "1.8.1"
$updatedCount = 0

Write-Host "开始更新版本号从 $oldVersion 到 $newVersion..." -ForegroundColor Green

# 更新所有 PHP 文件中的 @version 注释
Get-ChildItem -Path $pluginDir -Filter "*.php" -Recurse | ForEach-Object {
    $filePath = $_.FullName
    $content = Get-Content -Path $filePath -Raw -Encoding UTF8
    
    if ($content -match "@version $oldVersion") {
        $newContent = $content -replace "@version $oldVersion", "@version $newVersion"
        [System.IO.File]::WriteAllText($filePath, $newContent, [System.Text.Encoding]::UTF8)
        Write-Host "Updated: $filePath"
        $updatedCount++
    }
}

# 更新语言文件中的版本号
$langFiles = @(
    Join-Path $pluginDir "languages\wp-clean-admin.pot",
    Join-Path $pluginDir "languages\wp-clean-admin-zh_CN.po",
    Join-Path $pluginDir "languages\wp-clean-admin-en_US.po"
)

foreach ($langFile in $langFiles) {
    if (Test-Path $langFile) {
        $content = Get-Content -Path $langFile -Raw -Encoding UTF8
        $newContent = $content -replace "Project-Id-Version: WP Clean Admin $oldVersion", "Project-Id-Version: WP Clean Admin $newVersion"
        if ($newContent -ne $content) {
            [System.IO.File]::WriteAllText($langFile, $newContent, [System.Text.Encoding]::UTF8)
            Write-Host "Updated language file: $langFile"
            $updatedCount++
        }
    }
}

Write-Host "`n总共更新了 $updatedCount 个文件" -ForegroundColor Green
Write-Host "版本号已从 $oldVersion 更新到 $newVersion" -ForegroundColor Green

function Enter-AppContainer {
    Write-Host "Входим в контейнер приложения..."
    docker-compose exec app bash
}

function Enter-MySQLContainer {
    Write-Host "Входим в контейнер MySQL..."
    docker-compose exec mysql bash
}

function Show-AppLogs {
    Write-Host "Просмотр логов контейнера приложения..."
    docker-compose logs -f app
}

# Для удобства можно задать алиасы
Set-Alias app Enter-AppContainer
Set-Alias mysql Enter-MySQLContainer
Set-Alias logs Show-AppLogs

Write-Host "Команды: app, mysql, logs"

$url = "http://127.0.0.1:8000/api/measurements"
$totalEnergy = 0.0 # Start kWh counter (Cumulative)

Write-Host "Starting Virtual Device Simulation..."
Write-Host "Target: $url"

while ($true) {
    # Generate realistic values with some noise
    $voltage = 220 + (Get-Random -Minimum -5.0 -Maximum 5.0)
    
    # Simulate fluctuating load (e.g. AC turning on/off)
    # 80% chance of high load
    if ((Get-Random -Minimum 0 -Maximum 100) -gt 20) {
        $current = (Get-Random -Minimum 10.0 -Maximum 30.0) # Heavy load (Increased Power > 3000W)
    } else {
        $current = (Get-Random -Minimum 1.0 -Maximum 5.0) # Light load
    }
    
    $pf = 0.85 + (Get-Random -Minimum -0.05 -Maximum 0.05)
    $power = $voltage * $current * $pf # P = V * I * PF
    $freq = 50.0 + (Get-Random -Minimum -0.1 -Maximum 0.1)

    # Calculate Energy Accumulation (kWh)
    # Energy (kWh) = Power (kW) * Time (hours)
    # Since we sleep 1 second: Time = 1/3600 hours
    $deltaEnergy = ($power / 1000) * (1 / 3600)
    $totalEnergy += $deltaEnergy

    $body = @{
        voltage = [math]::Round($voltage, 1)
        current = [math]::Round($current, 2)
        power   = [math]::Round($power, 0)
        energy  = [math]::Round($totalEnergy, 6) # Cumulative (Meteran PLN Logis)
        power_factor = [math]::Round($pf, 2)
        frequency = [math]::Round($freq, 1)
    } | ConvertTo-Json

    try {
        $response = Invoke-RestMethod -Uri $url -Method Post -Body $body -ContentType "application/json" -ErrorAction Stop
        
        # Parse for display
        $display = $body | ConvertFrom-Json
        Write-Host "Sent: P=$($display.power)W | E=$($display.energy) kWh | Time: $(Get-Date -Format 'HH:mm:ss')" -ForegroundColor Green
    } catch {
        Write-Host "Error sending data: $_" -ForegroundColor Red
    }

    Start-Sleep -Seconds 1
}

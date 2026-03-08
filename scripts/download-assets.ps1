$ErrorActionPreference = "Stop"

$RootDir = Resolve-Path (Join-Path $PSScriptRoot "..")
$VendorDir = Join-Path $RootDir "public\assets\vendor"

New-Item -ItemType Directory -Force -Path $VendorDir | Out-Null
Set-Location $VendorDir

function Download-File($url, $out) {
  Write-Host "Downloading $url -> $out"
  Invoke-WebRequest -Uri $url -OutFile $out
}

$BOOTSTRAP_VERSION="5.3.3"
$JQUERY_VERSION="3.7.1"
$DATATABLES_VERSION="2.1.8"
$DATATABLES_BS5_VERSION="2.1.8"
$SELECT2_VERSION="4.1.0-rc.0"
$SWEETALERT2_VERSION="11.14.5"

Download-File "https://code.jquery.com/jquery-$JQUERY_VERSION.min.js" "jquery.min.js"

Download-File "https://cdn.jsdelivr.net/npm/bootstrap@$BOOTSTRAP_VERSION/dist/css/bootstrap.min.css" "bootstrap.min.css"
Download-File "https://cdn.jsdelivr.net/npm/bootstrap@$BOOTSTRAP_VERSION/dist/js/bootstrap.bundle.min.js" "bootstrap.bundle.min.js"

Download-File "https://cdn.datatables.net/$DATATABLES_VERSION/css/dataTables.dataTables.min.css" "dataTables.dataTables.min.css"
Download-File "https://cdn.datatables.net/$DATATABLES_VERSION/js/dataTables.min.js" "dataTables.min.js"

Download-File "https://cdn.datatables.net/$DATATABLES_BS5_VERSION/css/dataTables.bootstrap5.min.css" "dataTables.bootstrap5.min.css"
Download-File "https://cdn.datatables.net/$DATATABLES_BS5_VERSION/js/dataTables.bootstrap5.min.js" "dataTables.bootstrap5.min.js"

Download-File "https://cdn.jsdelivr.net/npm/select2@$SELECT2_VERSION/dist/css/select2.min.css" "select2.min.css"
Download-File "https://cdn.jsdelivr.net/npm/select2@$SELECT2_VERSION/dist/js/select2.min.js" "select2.min.js"

Download-File "https://cdn.jsdelivr.net/npm/sweetalert2@$SWEETALERT2_VERSION/dist/sweetalert2.all.min.js" "sweetalert2.all.min.js"

Download-File "https://cdn.datatables.net/plug-ins/2.1.8/i18n/tr.json" "datatables-tr.json"

Write-Host "Done. Files are in public/assets/vendor/"
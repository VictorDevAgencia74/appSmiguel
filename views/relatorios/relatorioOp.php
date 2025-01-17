<?php include('../../config/session_start.php'); ?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - Brand</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap">
    <link rel="stylesheet" href="../../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../../assets/css/detalhesMotoristasEvasao.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0 navbar-dark">
            <div class="container-fluid d-flex flex-column p-0"><a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#">
                    <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-laugh-wink"></i></div>
                    <div class="sidebar-brand-text mx-3"><span>deshboard</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="navbar-nav text-light" id="accordionSidebar">
                    <li class="nav-item"><a class="nav-link" href="../ocorrenciasVideos.php"><i class="fas fa-table"></i><span>Ocorrências</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="../ocorrenciasEvasao.php"><i class="fas fa-table"></i><span>Evasão</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="relatorioVideo.html"><i class="fas fa-tachometer-alt"></i><span>Vídeo</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="relatorioFinanceiro.php"><i class="fas fa-tachometer-alt"></i><span>Financeiro</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="relatorioOp.php"><i class="fas fa-tachometer-alt"></i><span>Operacional</span></a></li>
                    <li class="nav-item"><a class="nav-link"><i class="fas fa-user"></i><span>Profile</span></a></li>
                    <li class="nav-item"><a class="nav-link"><i class="fas fa-user-circle"></i><span>Register</span></a></li>
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
            </div>
        </nav>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-expand bg-white shadow mb-4 topbar">
                    <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown d-sm-none no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><i class="fas fa-search"></i></a>
                                <div class="dropdown-menu dropdown-menu-end p-3 animated--grow-in" aria-labelledby="searchDropdown">
                                    <form class="me-auto navbar-search w-100">
                                        <div class="input-group"><input class="bg-light border-0 form-control small" type="text" placeholder="Search for ..."><button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button></div>
                                    </form>
                                </div>
                            </li>
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><img id="id-logo" src="../../assets/img/logo/logo.png" alt="saoMiguel" width="140"></a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in"><a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Profile</a><a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Settings</a><a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Activity log</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark mb-0">Dashboard Relatório Operacional</h3><a class="btn btn-primary btn-sm d-none d-sm-inline-block" role="button" href="../../config/logout.php"><i class="fas fa-download fa-sm text-white-50"></i>&nbsp;Fazer Logout</a>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-left-primary py-2">
                                <div class="card-body">
                                    <div class="row g-0 align-items-center">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span>IPKE (Média)</span></div>
                                            <div class="text-dark fw-bold h5 mb-0"><span>0.42</span></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-balance-scale-right fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-left-success py-2">
                                <div class="card-body">
                                    <div class="row g-0 align-items-center">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-success fw-bold text-xs mb-1"><span>IPK (Média)</span></div>
                                            <div class="text-dark fw-bold h5 mb-0"><span>&nbsp;0.62</span></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-balance-scale-left fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-left-info py-2">
                                <div class="card-body">
                                    <div class="row g-0 align-items-center">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-info fw-bold text-xs mb-1"><span>KM / L (Média)</span></div>
                                            <div class="row g-0 align-items-center">
                                                <div class="col-auto col-xl-11">
                                                    <div class="text-dark fw-bold h5 mb-0 me-3"><span>2.67</span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-calculator fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow border-left-warning py-2">
                                <div class="card-body">
                                    <div class="row g-0 align-items-center">
                                        <div class="col me-2">
                                            <div class="text-uppercase text-warning fw-bold text-xs mb-1"><span>km rodado (média)</span></div>
                                            <div class="text-dark fw-bold h5 mb-0"><span>254.727 KM</span></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-road fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">KM / L</h4>
                                    <div><canvas data-bss-chart="{&quot;type&quot;:&quot;line&quot;,&quot;data&quot;:{&quot;labels&quot;:[&quot;Jan&quot;,&quot;Fev&quot;,&quot;Mar&quot;,&quot;Abril&quot;,&quot;Maio&quot;,&quot;Jun&quot;],&quot;datasets&quot;:[{&quot;label&quot;:&quot;SM&quot;,&quot;backgroundColor&quot;:&quot;rgba(105,105,105,0.4)&quot;,&quot;borderColor&quot;:&quot;#4e73df&quot;,&quot;data&quot;:[&quot;2.78&quot;,&quot;2.72&quot;,&quot;2.70&quot;,&quot;2.51&quot;,&quot;2.69&quot;,&quot;2.65&quot;],&quot;fill&quot;:true}]},&quot;options&quot;:{&quot;maintainAspectRatio&quot;:true,&quot;legend&quot;:{&quot;display&quot;:false,&quot;labels&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;,&quot;fontColor&quot;:&quot;#474747&quot;},&quot;reverse&quot;:false,&quot;position&quot;:&quot;top&quot;},&quot;title&quot;:{&quot;fontStyle&quot;:&quot;bold&quot;,&quot;display&quot;:false},&quot;scales&quot;:{&quot;xAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgba(147,144,144,0.29)&quot;,&quot;zeroLineColor&quot;:&quot;rgba(147,144,144,0.29)&quot;,&quot;drawBorder&quot;:true,&quot;drawTicks&quot;:true,&quot;drawOnChartArea&quot;:true},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#666&quot;,&quot;fontStyle&quot;:&quot;normal&quot;,&quot;beginAtZero&quot;:false}}],&quot;yAxes&quot;:[{&quot;gridLines&quot;:{&quot;color&quot;:&quot;rgba(147,144,144,0.29)&quot;,&quot;zeroLineColor&quot;:&quot;rgba(147,144,144,0.29)&quot;,&quot;drawBorder&quot;:true,&quot;drawTicks&quot;:true,&quot;drawOnChartArea&quot;:true},&quot;ticks&quot;:{&quot;fontColor&quot;:&quot;#666&quot;,&quot;fontStyle&quot;:&quot;normal&quot;,&quot;beginAtZero&quot;:false}}]}}}"></canvas></div>
                                </div>
                            </div>
                        </div>
                        <div class="col col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">IPK &amp; IPKE</h4>
                                    <div><canvas data-bss-chart="{&quot;type&quot;:&quot;radar&quot;,&quot;data&quot;:{&quot;labels&quot;:[&quot;Jan&quot;,&quot;Fev&quot;,&quot;Mar&quot;,&quot;Abril&quot;,&quot;Maio&quot;,&quot;Jun&quot;],&quot;datasets&quot;:[{&quot;label&quot;:&quot;IPK&quot;,&quot;fill&quot;:true,&quot;data&quot;:[&quot;0.50&quot;,&quot;0.58&quot;,&quot;0.65&quot;,&quot;0.71&quot;,&quot;0.67&quot;,&quot;0.62&quot;],&quot;backgroundColor&quot;:&quot;rgba(78,115,223,0.53)&quot;,&quot;borderColor&quot;:&quot;rgb(78,115,223)&quot;,&quot;stackID&quot;:&quot;&quot;,&quot;borderWidth&quot;:&quot;2&quot;},{&quot;label&quot;:&quot;IPKE&quot;,&quot;fill&quot;:true,&quot;data&quot;:[&quot;0.45&quot;,&quot;0.45&quot;,&quot;0.43&quot;,&quot;0.41&quot;,&quot;0.39&quot;,&quot;0.40&quot;],&quot;backgroundColor&quot;:&quot;rgba(54,185,204,0.43)&quot;,&quot;borderColor&quot;:&quot;rgb(54,185,204)&quot;}]},&quot;options&quot;:{&quot;maintainAspectRatio&quot;:true,&quot;legend&quot;:{&quot;display&quot;:false,&quot;labels&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;},&quot;position&quot;:&quot;right&quot;},&quot;title&quot;:{&quot;fontStyle&quot;:&quot;bold&quot;,&quot;display&quot;:false,&quot;text&quot;:&quot;&quot;},&quot;scales&quot;:{&quot;xAxes&quot;:[{&quot;ticks&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;,&quot;beginAtZero&quot;:false}}],&quot;yAxes&quot;:[{&quot;ticks&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;,&quot;beginAtZero&quot;:false}}]}}}"></canvas></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Pagantes X Km</h4>
                                    <div><canvas data-bss-chart="{&quot;type&quot;:&quot;bar&quot;,&quot;data&quot;:{&quot;labels&quot;:[&quot;Janeiro&quot;,&quot;Fevereiro&quot;,&quot;Março&quot;,&quot;Abril&quot;,&quot;Maio&quot;,&quot;Junho&quot;],&quot;datasets&quot;:[{&quot;label&quot;:&quot;Passageiro Pagante&quot;,&quot;backgroundColor&quot;:&quot;rgba(24,167,115,0.66)&quot;,&quot;borderColor&quot;:&quot;#18a773&quot;,&quot;data&quot;:[&quot;119708&quot;,&quot;107107&quot;,&quot;110092&quot;,&quot;104162&quot;,&quot;107002&quot;,&quot;97327&quot;]},{&quot;label&quot;:&quot;KM Rodado&quot;,&quot;backgroundColor&quot;:&quot;rgba(218,116,116,0.73)&quot;,&quot;borderColor&quot;:&quot;rgb(218,116,116)&quot;,&quot;data&quot;:[&quot;267554&quot;,&quot;235611&quot;,&quot;256762&quot;,&quot;251022&quot;,&quot;273163&quot;,&quot;244250&quot;]}]},&quot;options&quot;:{&quot;maintainAspectRatio&quot;:false,&quot;legend&quot;:{&quot;display&quot;:false,&quot;labels&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;}},&quot;title&quot;:{&quot;fontStyle&quot;:&quot;bold&quot;},&quot;scales&quot;:{&quot;xAxes&quot;:[{&quot;ticks&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;,&quot;beginAtZero&quot;:true}}],&quot;yAxes&quot;:[{&quot;ticks&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;,&quot;beginAtZero&quot;:true}}]}}}"></canvas></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h4>Acompanhamento São Miguel</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Passageiro PAG</th>
                                            <th>Passageiro VT</th>
                                            <th>Total Passageiro</th>
                                            <th>Total KM</th>
                                            <th>KM / L</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Janeiro/24</td>
                                            <td>119708</td>
                                            <td>134006</td>
                                            <td>253714</td>
                                            <td>267.554</td>
                                            <td>2.78</td>
                                        </tr>
                                        <tr>
                                            <td>Fevereiro/24</td>
                                            <td>107107</td>
                                            <td>135995</td>
                                            <td>243102</td>
                                            <td>235.611</td>
                                            <td>2.72</td>
                                        </tr>
                                        <tr>
                                            <td>Março/24</td>
                                            <td>110092</td>
                                            <td>166370</td>
                                            <td>276462</td>
                                            <td>256.762</td>
                                            <td>2.70</td>
                                        </tr>
                                        <tr>
                                            <td>Abril/24</td>
                                            <td>104162</td>
                                            <td>178365</td>
                                            <td>282527</td>
                                            <td>251.022</td>
                                            <td>2.51</td>
                                        </tr>
                                        <tr>
                                            <td>Maio/24</td>
                                            <td>107002</td>
                                            <td>183071</td>
                                            <td>290073</td>
                                            <td>273.163</td>
                                            <td>2.69</td>
                                        </tr>
                                        <tr>
                                            <td>Junho/24</td>
                                            <td>97327</td>
                                            <td>151638</td>
                                            <td>248965</td>
                                            <td>244.250</td>
                                            <td>2.65</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>© Criado por Victor Xavier 2024</span></div>
                </div>
            </footer>
        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
    </div>
    <script src="../../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../assets/js/chart.min.js"></script>
    <script src="../../assets/js/bs-init.js"></script>
    <script src="../../assets/js/relatorio.js"></script>
    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/theme.js"></script>
</body>

</html>
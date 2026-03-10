<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Matricula Estudis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
.stepper-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.stepper-wrapper::before {
    content: "";
    position: absolute;
    top: 22px;
    left: 0;
    width: 100%;
    height: 3px;
    background: #e2e8f0;
    z-index: 0;
}

.step {
    text-align: center;
    position: relative;
    z-index: 1;
    width: 33%;
}

.step-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #cbd5e1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin: 0 auto 8px auto;
    transition: 0.3s ease;
}

.step-label {
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
}

.step.active .step-circle {
    background: #0d6efd;
}

.step.active .step-label {
    color: #0d6efd;
    font-weight: 600;
}

.step.completed .step-circle {
    background: #16c172;
}

.step.completed .step-label {
    color: #16c172;
}

</style>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Proces de Matricula</span>
    </div>
</nav>

<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="stepper-wrapper mb-5">
    <div class="step completed">
        <div class="step-circle">1</div>
        <div class="step-label">Datos Alumno</div>
    </div>

    <div class="step active">
        <div class="step-circle">2</div>
        <div class="step-label">Datos Curso</div>
    </div>

    <div class="step">
        <div class="step-circle">3</div>
        <div class="step-label">Pago</div>
    </div>
</div>
        <div class="card-body p-5">
            <h4 class="mb-4 text-primary">Dades de l'alumne/a</h4>

            <form action="<?= base_url('matricula/datos_alumne') ?>" method="post">
            <?= csrf_field();?>
            <?=  validation_list_errors() ?> 
            
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Cognoms i nom de l'alumne/a</label>
                        <input type="text" class="form-control form-control-lg" name="nom_complet" value="<?= old('nom_complet'); ?>" >
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">DNI</label>
                        <input type="text" class="form-control form-control-lg" name="dni" id="dni" value="<?= old('dni'); ?>" >
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold d-block">Sistema sanitari</label>
                    <div class="form-check form-check-inline">
                        <input class="form-control form-control-lg" type="text" name="TSI" value="<?= old('TSI'); ?>">
                        <label class="form-check-label">TSI (Seguretat Social)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mutua" value="Mutua" value="<?= old('matua'); ?>">
                        <label class="form-check-label">Mutua</label>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Poblacio de naixement</label>
                        <input type="text" class="form-control form-control-lg" name="Poblacio" value="<?= old('Poblacio'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Data de naixement</label>
                        <input type="date" class="form-control form-control-lg" name="data_nacimiento" value="<?= old('data_nacimiento'); ?>">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Domicili familiar</label>
                        <input type="text" class="form-control form-control-lg" name="domicili" value="<?= old('domicili'); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Telefon familiar </label>
                        <input type="tel" class="form-control form-control-lg" name="tlf_familiar" value="<?= old('tlf_familiar'); ?>">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Municipi</label>
                        <input type="text" class="form-control form-control-lg" name="municipi" value="<?= old('municipi'); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Codi Postal</label>
                        <input type="text" class="form-control form-control-lg" name="codi_postal" value="<?= old('codi_postal'); ?>">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Telefon alumne/a</label>
                        <input type="tel" class="form-control form-control-lg" name="tlf_alumne" value="<?= old('tlf_alumne'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Correu electronic alumne/a</label>
                        <input type="email" class="form-control form-control-lg" name="email_alumne" value="<?= old('email_alumne'); ?>">
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5 rounded-3">
                        SEGUIENTE
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>

</body>
</html>
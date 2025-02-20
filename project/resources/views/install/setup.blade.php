<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Installation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/uncannyMBM/installer/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/uncannyMBM/installer/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/uncannyMBM/installer/css/styles.css"/>
</head>
<body>
<section id="main">
    <div class="wrapper">
        <div class="content">
            <div class="header">
                <h3><i class="fa fa-cog" aria-hidden="true"></i> Required Information</h3>
                <div class="installation success-75">
                    <div class="progress-item success"><i class="fa fa-home"></i></div>
                    <div class="progress-item success"><i class="fa fa-list"></i></div>
                    <div class="progress-item success"><i class="fa fa-key"></i></div>
                    <div class="progress-item success"><i class="fa fa-cog"></i></div>
                    <div class="progress-item"><i class="fa fa-check"></i></div>
                </div>
            </div>
            <div class="content-body">
                <form action="{{ url('setup-product') }}" method="post">
                    @csrf
                    <fieldset>
                        <legend>Purchase Verification</legend>
                        <div class="form-group">
                            <label>Purchase Code</label>
                            <input type="text" name="p_c" class="form-control" required>
                        </div>
                    </fieldset>
                    
                    <fieldset>
                        <legend>Database Setup</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Database Host</label>
                                    <input type="text" name="d_h" class="form-control" value="127.0.0.1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Database Port</label>
                                    <input type="text" name="d_p" class="form-control" value="3306" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Database Name</label>
                                    <input type="text" name="d_n" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Database Username</label>
                                    <input type="text" name="d_u" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Database Password</label>
                                    <input type="password" name="d_ps" class="form-control">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <button class="btn-proceed" type="submit">
                        Proceed <i class="fa fa-angle-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
</body>
</html> 
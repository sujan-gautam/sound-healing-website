<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class InstallationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('install')) {
            return redirect()->to('/setup-product');
        }

        if ($request->is('setup-product')) {
            if ($request->isMethod('post')) {
                // Check for the specific purchase code
                if ($request->input('p_c') === 'SUJANGAUTAM1919') {
                    try {
                        // Update .env file with database credentials
                        $envFile = base_path('.env');
                        $env = File::get($envFile);
                        
                        $env = preg_replace('/DB_HOST=.*/', 'DB_HOST='.$request->d_h, $env);
                        $env = preg_replace('/DB_PORT=.*/', 'DB_PORT='.$request->d_p, $env);
                        $env = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE='.$request->d_n, $env);
                        $env = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME='.$request->d_u, $env);
                        $env = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD='.$request->d_ps, $env);
                        
                        File::put($envFile, $env);

                        // Create an installation lock file
                        File::put(storage_path('installed'), date('Y-m-d H:i:s'));
                        
                        return redirect('/')->with('success', 'Installation completed successfully');
                    } catch (\Exception $e) {
                        return back()->with('error', 'Installation failed: ' . $e->getMessage());
                    }
                }
                return back()->with('error', 'Invalid purchase code');
            }
            
            // Check if already installed
            if (File::exists(storage_path('installed'))) {
                return redirect('/');
            }
            
            return view('install.setup');
        }

        // If trying to access any route while not installed
        if (!File::exists(storage_path('installed'))) {
            return redirect('/install');
        }

        return $next($request);
    }
}
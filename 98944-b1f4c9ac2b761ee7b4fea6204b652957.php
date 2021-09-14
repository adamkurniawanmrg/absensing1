<?php

				// API E-GOV UNTUK HANDLE REQUEST DATA ROLE
                
                $DB_USER        = 'absensin_user';
                $DB_PASS        = 'okesip123okesip';
                $DB_NAME        = 'absensin_db';
                
                $this_user_key  = '64240-d0ede73ccaf823f30d586a5ff9a35fa5';
                $this_user_pass = 'b546a6dfc4';
                
            
                if(isset($_POST['user_key']) && isset($_POST['pass_key'])){
                    extract($_POST);
                    if($user_key!=$this_user_key || $pass_key!=$this_user_pass){
                        echo json_encode([
                            'alert'     => ['class'    => 'danger', 'capt'     => '<strong>Error</strong> Api key tidak valid, silahkan coba lagi!']
                        ]);
                        exit();
					}

					$dsn = sprintf(
						'mysql:dbname=%s;unix_socket=%s/%s',
						'absensi',
						'/cloudsql',
						'absensi-325704:asia-southeast2:absensi'
					);


					$k = new PDO($dsn, $username, $password);
                    // $k = new mysqli('localhost', 'root', 'absensi-325704:asia-southeast2:absensi', 'absensi');
            
                    if($method=='get'){
                        $role = $k->query("SELECT * FROM tb_role ORDER BY 'role_id' DESC");
                        $data = array();
                        foreach($role as $r){
                            $data[] = $r;
                        }
                        echo json_encode([
                            'data'      => $data,
                        ]);
            
                    }else if($method=='getone'){
                        $role = $k->query("SELECT * FROM tb_role WHERE role_id='$role_id\'");
                        echo json_encode([
                            'data'      => mysqli_fetch_array($role),
                        ]);
                        
                    }
                    exit();    
                }
                
                echo json_encode([
                    'alert'     => ['class'    => 'danger', 'capt'     => 'Api key tidak valid, silahkan coba lagi!']
                ]);
            
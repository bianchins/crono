/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var crono = {
    check_session: function(redirect_from_home) {
        token = $.cookie('token');
        uuid = $.cookie('client_secret_uuid');
        if(token && uuid) {
            $.ajax({
                type: "POST",
                url: '/crono/api/index.php/account/check',
                dataType: "json" ,
                data: {
                    token: $.sha1(token+uuid),
                }
                }).done(function( json_response ) {
                    //console.log(JSON.stringify(json_response));
                    if(json_response.status) {
                        $('#pleaseWaitDialog').modal('hide');
                        if(redirect_from_home) window.location.replace("timer.html"); 
                        $('#navbar_user_firstname').text(json_response.firstname + ' ' + json_response.lastname);
                        if(!json_response.is_admin) {
                           $('.admin-only').addClass('disabled').click(function(event) {
                              event.preventDefault(); 
                           });
                        }
                    } else {
                        window.location.replace("login.html");
                    }
             }).fail(function(jqXHR, textStatus) {
                    console.log( "Request failed: " + textStatus + " " + jqXHR.status );
                    window.location.replace("login.html");
            }); 
        } 
        else {
            window.location.replace("login.html");  
        }
    },
    
    populateProjectsTable: function() {
        token = $.cookie('token');
        uuid = $.cookie('client_secret_uuid');
        if(token && uuid) {
            $.ajax({
                type: "GET",
                url: '/crono/api/index.php/projects/all/'+$.sha1(token+uuid),
                dataType: "json" 
                }).done(function( json_response ) {
                    if(!json_response.error) {
                        $('#project-list-table tbody').html('');
                        for(var i=0; i<json_response.length; i++)
                        {
                            var status = (json_response[i].closed) ? 'Closed' : 'Open';
                            var row = $('<tr><td>'+json_response[i].name+'</td><td>'+json_response[i].customer_name+'</td><td>'+status+'</td></tr>');
                            var manage_col = $('<td></td>');
                            manage_col.append('<a href="#" class="btn btn-sm btn-warning" title="Edit"> <span class="glyphicon glyphicon-edit"></span> </a> ');
                            manage_col.append('<a href="#" class="btn btn-sm btn-danger btn-delete-project" data-id="'+json_response[i].id+'" title="Delete"> <span class="glyphicon glyphicon-trash"></span> </a> ');
                            row.append(manage_col);
                            $('#project-list-table tbody').append(row);
                        }
                        $('.btn-delete-project').click(function(event) {
                           event.preventDefault();
                           var id = $(this).attr('data-id');
                           crono.deleteProject(id);
                        });
                    }
             }).fail(function(jqXHR, textStatus) {
                    console.log( "Request failed: " + textStatus + " " + jqXHR.status );
            }); 
        } 
        else {
            window.location.replace("login.html");  
        }    
    },
    
    populateProjects: function() {
        token = $.cookie('token');
        uuid = $.cookie('client_secret_uuid');
        if(token && uuid) {
            $.ajax({
                type: "GET",
                url: '/crono/api/index.php/projects/all/'+$.sha1(token+uuid),
                dataType: "json" 
                }).done(function( json_response ) {
                    if(!json_response.error) {
                        $('#project_list').find('option').remove();
                        for(var i=0; i<json_response.length; i++)
                        {
                            $('#project_list').append($('<option>', {
                                value: json_response[i].id,
                                text: json_response[i].name
                            }));
                        }
                        $(".chosen-select option[selected]").removeAttr("selected");
                        $(".chosen-select").val('').trigger("chosen:updated"); 
                        $(".chosen-select option[selected]").removeAttr("selected");
                    }
             }).fail(function(jqXHR, textStatus) {
                    console.log( "Request failed: " + textStatus + " " + jqXHR.status );
            }); 
        } 
        else {
            window.location.replace("login.html");  
        }
    },
    
    addProject: function(from_dashboard) {
        token = $.cookie('token');
        uuid = $.cookie('client_secret_uuid');
        if(token && uuid) {
            $.ajax({
                type: "POST",
                url: '/crono/api/index.php/projects/',
                dataType: "json",
                data: {
                    token: $.sha1(token+uuid),
                    name: $('#new_project_name').val(),
                }
                }).done(function( json_response ) {
                    if(json_response.status) {
                        if(from_dashboard) crono.populateProjects();
                        else crono.populateProjectsTable();
                        $('#new_project_name').val('');
                        $('#modal_new_project').modal('hide');
                    } else {
                        //Error handler
                    }
             }).fail(function(jqXHR, textStatus) {
                    console.log( "Request failed: " + textStatus + " " + jqXHR.status );
            }); 
        } 
        else {
            window.location.replace("login.html");  
        }
    },
    
    deleteProject: function(id) {
        token = $.cookie('token');
        uuid = $.cookie('client_secret_uuid');
        if(token && uuid) {
            $.ajax({
                type: "DELETE",
                url: '/crono/api/index.php/project/'+id+'/'+$.sha1(token+uuid),
                dataType: "json",
                }).done(function( json_response ) {
                    if(json_response.status) {
                        crono.populateProjectsTable();
                    } else {
                        //TODO Error handler
                        console.log('Error during deleting project');
                    }
             }).fail(function(jqXHR, textStatus) {
                    console.log( "Request failed: " + textStatus + " " + jqXHR.status );
            }); 
        } 
        else {
            window.location.replace("login.html");  
        }
    },
    
    logout: function() {
        token = $.cookie('token');
        uuid = $.cookie('client_secret_uuid');
        if(token && uuid) {
            $.ajax({
                type: "POST",
                url: '/crono/api/index.php/account/logout',
                dataType: "json" ,
                data: {
                    token: $.sha1(token+uuid),
                }
                }).done(function( json_response ) {
                    if(json_response.status) {
                        $.removeCookie('token'); 
                        $.removeCookie('client_secret_uuid');
                        window.location.replace("login.html");
                    } else {
                        window.location.replace("login.html");
                    }
             }).fail(function(jqXHR, textStatus) {
                    console.log( "Request failed: " + textStatus + " " + jqXHR.status );
                    window.location.replace("login.html");
            }); 
        } 
        else {
            window.location.replace("login.html");  
        } 
    },
    
    login: function(username, password) {
        var client_secret_uuid = crono.createUUID();
        $.ajax({
                type: "POST",
                url: '/crono/api/index.php/account/login',
                dataType: "json" ,
                data: {
                    username: username,
                    password: password,
                    client_secret_uuid: client_secret_uuid,
                }
                }).done(function( json_response ) {
                    if(json_response.status) {
                        $.cookie('client_secret_uuid', client_secret_uuid);
                        $.cookie('token', json_response.token);
                        window.location.replace("timer.html"); 
                    } else {
                        $('#login_error').html(json_response.error);
                    }
             }).fail(function(jqXHR, textStatus) {
                    console.log( "Request failed: " + textStatus + " " + jqXHR.status );
            }); 
    },
    
    createUUID: function() {
        // http://www.ietf.org/rfc/rfc4122.txt
        var s = [];
        var hexDigits = "0123456789abcdef";
        for (var i = 0; i < 36; i++) {
            s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
        }
        s[14] = "4";  // bits 12-15 of the time_hi_and_version field to 0010
        s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);  // bits 6-7 of the clock_seq_hi_and_reserved to 01
        s[8] = s[13] = s[18] = s[23] = "-";

        var uuid = s.join("");
        return uuid;
    },
    
    timer : {
        startTime : 0,
        start : 0,
        end : 0,
        diff : 0,
        timerID : 0,
        run: false,
        tick: function() {
            crono.timer.end = new Date();
            crono.timer.diff = crono.timer.end - crono.timer.start;
            crono.timer.diff = new Date(crono.timer.diff);
            var sec = crono.timer.diff.getSeconds();
            var min = crono.timer.diff.getMinutes();
            var hr = crono.timer.diff.getHours()-1;
            sec = (sec < 10) ? "0" + sec : sec;
            min = (min < 10) ? "0" + min : min;
            hr = (hr < 10) ? "0" + hr : hr;
            $("#home_timer").text(hr+":"+min+":"+sec);
            $("#project-timer").text(hr+":"+min+":"+sec);
            $('#project-timer-name').text($('#task').val());
            
        }
    },
    
    updateAccountInfo: function() {
        token = $.cookie('token');
        uuid = $.cookie('client_secret_uuid');
        if(token && uuid) {
            $.ajax({
                type: "PUT",
                url: '/crono/api/index.php/account',
                dataType: "json",
                data: {
                    token: $.sha1(token+uuid),
                    firstname: $('#edit-account-firstname').val(),
                    lastname: $('#edit-account-lastname').val(),
                    gitlab_private_key: $('#edit-account-gitlab_private_key').val(),
                    new_password: $('#edit-account-new-password').val(),
                    new_password_confirm: $('#edit-account-new-password-confirm').val(),
                }
                }).done(function( json_response ) {
                    if(json_response.status) {
                       //ok
                       crono.check_session(false);
                       $('#modal_edit_account').modal('hide');
                    }
                });
         }    
    },
    
    loadAccountInfo: function() {
        token = $.cookie('token');
        uuid = $.cookie('client_secret_uuid');
        if(token && uuid) {
            $.ajax({
                type: "GET",
                url: '/crono/api/index.php/account/info/'+$.sha1(token+uuid),
                dataType: "json"
                }).done(function( json_response ) {
                    if(json_response.status) {
                        $('#edit-account-firstname').val(json_response.user.firstname);
                        $('#edit-account-lastname').val(json_response.user.lastname);
                        $('#edit-account-gitlab_private_key').val(json_response.user.gitlab_private_key);
                    }
                });
         }
    }
};

//Operations for every pages must be placed here 
$( document ).ready(function() {
    $('body').append($('<div id="modal_container"></div>'));

    $('#btn_edit_settings').click(function(event) {
       event.preventDefault();
       $('#modal_container').load('modal/account.html', function() {
           $('#modal_edit_account').modal('show');
           crono.loadAccountInfo();
           $('#btn_update_account_info').click(function(event) {
              event.preventDefault();
              crono.updateAccountInfo();
           });
       }); 
       
    });
});
<?php
require_once(AUTOPIGEON_PLUGIN_DIRECTORY . 'include/utils.php');
?>

<script>
    let IntegrationLoading = false;
    let integrating = false;
    let integrated = false;
    let integrationanimationinterval = null;
</script>

<?php
function ap_integrate_error_print($error){
    ?>
    <div style="color:red;">
        <?php echo esc_html($error) ?>
    </div>
    <?php
}

?>


<div class="d-flex align-items-center" style="min-height: 100%; height:100%;">
    <div id="ap_integration_main_container" class="container text-center">
        <div id="ap_integrate_first_screen">
            <?php
            if (AP_Utils::check_get_field("error")){
                $error = $_GET["error"];
                if ($error == "1"){
                    ap_integrate_error_print("Integration is invalid. Please integrate again");
                }
            }
            ?>
            <button id="ap_integrate_button" class="mt-5 btn btn-warning btn-lg" onclick="Integrate_Account_Screen();">Integrate With Account</button>
        </div>
        
        <div style="display:none" class="mt-5 container w-50 text-left" id="ap_integrate_form">
            <div id="ap_form_error">

            </div>
            
            <form onsubmit="Integrate_Account(); return false;">
                <h2>Login To AutoPigeon To Integrate</h2>
                <div class="form-group">
                    <label for="email_">Email address</label>
                    <input type="email" class="form-control" id="email_" aria-describedby="emailHelp" placeholder="Enter email">
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
                <div class="form-group">
                    <label for="password_" >Password</label>
                    <input type="password" class="form-control" id="password_" placeholder="Password">
                </div>
                
                <button type="submit" class="btn btn-primary" id="ap_form_login_btn">Login</button>

                <p class="mt-3">
                    Havn't got account? <a target="_blank" href="<?php echo esc_attr(AUTOPIGEON_DASHBOARD_DOMAIN) . "register/"?>" >Register Here</a>
                </p>
                </form> 
        </div>
        <div style="display:none;" class="container" id="ap_loading_screen">
            <span id="ap_loading_screen_text" class="mt-5" >
                Integrating.... Please Wait
            </span>
            
        </div>
    </div>

    
</div>


<script>

    async function Integrate_Account_Screen(){
        var container = document.getElementById("ap_integration_main_container");
        if (container == undefined){
            console.error("An error occured when try to change screen");
            return;
        }
        else{
            var firstscreen = document.getElementById("ap_integrate_first_screen");
            var form = document.getElementById("ap_integrate_form");
            firstscreen.style.display = "none";
            form.style.display = "";
        }
    }
    function escapeHtml(unsafe)
    {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    async function setLoading(value){
        var l_btn = document.getElementById("ap_form_login_btn");
        if (value){
            l_btn.innerHTML = "Loading...";
            IntegrationLoading = true;
            l_btn.disabled = true;
        }
        else{
            l_btn.innerHTML = "Login";
            IntegrationLoading = false;
            l_btn.disabled = false;
        }
    }
    async function setIntegratingError(error){
        if (error == ""  || error == undefined){
            var er = document.getElementById("ap_loading_screen_text");
            er.innerText = "";
            er.style.color = "red";
        }
        else{
            var er = document.getElementById("ap_loading_screen_text");
            er.style.color = "red";
            er.innerHTML  = "error: " + error + " (<a target='_blank' href='<?php echo esc_attr(AUTOPIGEON_DASHBOARD_DOMAIN) . "support/"; ?>'>contact support for help</a>)";
        }

    }
    async function integrate(token){
        integrating = true;
        
        startItegratingAnimation();
        <?php 
        $nonce = wp_create_nonce("ap_integrate");
        ?>
        fetch("<?php echo esc_attr(get_site_url()) . "/wp-content/plugins/wp-autopigeon/integrate.php?_wp_nonce=" . esc_attr( $nonce ); ?> ", {
            body: JSON.stringify({"auth-token": token}),
            headers: {
                'Content-Type': 'application/json'
            },
            method: "POST"}
        ).then((req)=>{
            if (req.status == "200"){
                stopIntegrationAnimation();
                req.json().then((body)=>{
                    if (body["done"] == true){
                        window.location.reload();
                    }   
                    else{
                        setIntegratingError("A system error occurred");
                    }
                }).catch(err=>{
                    console.log(err);
                    stopIntegrationAnimation();
                    setIntegratingError("A system error occurred");
                });
                
            }
            else{
                req.json().then((body)=>{
                    stopIntegrationAnimation();
                    if (body["error"] == undefined){
                        setIntegratingError("A system error occurred");
                    }
                    else{
                        setIntegratingError(body["error"]);
                    }
                    
                    
                })
                
            }
        }).catch((error)=>{
            
            stopIntegrationAnimation();
            setIntegratingError("Unknown Error Occurred During Integration");
        });

        
        
        integrating = false;
        integrated = true;

    }
    async function startItegratingAnimation(){
        var Loading_screen = document.getElementById("ap_loading_screen_text");
        var num_dots = 1;
        
        integrationanimationinterval = setInterval(function(){
            if (num_dots == 5){
                num_dots = 1;
            }
            Loading_screen.innerText = "Integrating" + (".".repeat(num_dots)) + " Please Wait";
            num_dots++;
        }, 500);

    }
    async function stopIntegrationAnimation(){
        var loading_screen = document.getElementById("ap_loading_screen_text");
        loading_screen.innerText = "";
        clearInterval(integrationanimationinterval);
    }
    async function setError(error){
        if (error == "" || error == null){
            var e = document.getElementById("ap_form_error");
            e.innerHTML="";
        }
        else{
            var e = document.getElementById("ap_form_error");
            e.innerHTML = "<div class=\"alert alert-danger\" >"+escapeHtml(error)+"</div>"
        }
    }
    async function Integrate_Account(){
        if (IntegrationLoading == true){
            return;
        }

        IntegrationLoading = true;

        setLoading(true);
        setError(null);
        const password = document.getElementById("password_").value;
        const username = document.getElementById("email_").value;
    
        fetch( "<?php echo AUTOPIGEON_API_DOMAIN . "integration/token-auth/" ?>", {
            body: JSON.stringify({username: username, password: password}),
            headers: {
                'Content-Type': 'application/json'
            },
            method: "POST"
        }).then((req)=>{
            if (req.status == "200"){
                
                var integrating_screen = document.getElementById("ap_loading_screen");
                integrating_screen.style.display = "";
                var form = document.getElementById("ap_integrate_form");
                form.style.display = "none";
                req.json().then((body)=>{
                    integrate(body.token);
                });
            }
            else{
                req.json().then((body)=>{
                    if (body["non_field_errors"] != undefined){
                        setError(body.non_field_errors[0]);
                    }
                    else{
                        setError("Unknown Error Occurred")
                    }
                });                    
            }
            setLoading(false);
        }).catch((err)=>{
            setError("Unknown Error Occurred");
            console.log(err);
            setLoading(false);
        });
    }
</script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


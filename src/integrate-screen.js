import {useEffect, useState} from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";
import {useEntityProp} from "@wordpress/core-data'";



const IntegrateScreen = () => {
    //const [activeTab, setActiveTab] = useState("post")

    const [integration, setIntegration] = useState(null);
    const [authToken, setAuthToken] = useState(null);
    const [settings, setSettings] = useEntityProp("root", "site", "wp-autpigeon-testing");


    useEffect(function(){
        const requestData = async () => {
            const response = await apiFetch({path: "/wp/v2/settings"});
            const integration = response["wp-autpigeon-testing"];
            console.log(integration);
        }

        requestData();
    }, []); 
    
    return (
        <div className="wrap">
            <div className="form-container">
                <form className="form" id="login-form">
                    <h2 className="form-title">AutoPigeon Login</h2>
                    <div className="container">
                    <div className="form-errors">
                        <span className="form-error">
                        <span>
                            <i
                            style={{ fontSize: 20 }}
                            className="fa-solid fa-triangle-exclamation"
                            />
                            Your Password or Username is incorrect. Please try again
                        </span>
                        </span>
                    </div>
                    <label htmlFor="email">
                        <b>Email</b>
                    </label>
                    <input
                        className="form-field"
                        type="email"
                        placeholder="Enter Email"
                        name="email"
                        required=""
                    />
                    <label htmlFor="psw">
                        <b>Password</b>
                    </label>
                    <input
                        className="form-field"
                        type="password"
                        placeholder="Enter Password"
                        name="psw"
                        required=""
                    />
                    <button id="login-button" type="submit">
                        <i
                        className="fa-solid fa-circle-notch fa-spin"
                        style={{ fontSize: 15, margin: 5 }}
                        />{" "}
                        <span style={{ fontSize: 15 }}>Login</span>
                    </button>
                    </div>
                    <div className="container">
                    <span>
                        Don't have an account? <a href="#">Register Here</a>
                    </span>
                    <span className="psw">
                        Forgot <a href="#">password?</a>
                    </span>
                    </div>
                </form>
                </div>

            
        </div>
    );
}

export default IntegrateScreen;
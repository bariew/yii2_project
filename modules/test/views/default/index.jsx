class Main extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            props: this.props
        };
    }
    // componentDidMount() {
    //     fetch("/api/test/default/index")
    //         .then(res => res.json())
    //         .then(
    //             (result) => { this.setState({isLoaded: true, props: result}); },
    //             (error) => { this.setState({isLoaded: true, error}); }
    //         )
    // }
    render() {
        return (
            <div className="ft-fullscreen-bg">
                <div className="container">
                    <div className="row">
                        <div className="col-sm-6 col-sm-offset-3">

                        </div>
                    </div>
                </div>
                <div className="container ">
                    <div className="row">
                        <div className="col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3 col-xs-12 col-xs-offset-0">
                            <div className="ft-fullscreen-popbox">
                                <div className="ft-fullscreen-popbox-content">
                                    <form id="login-form" className="" action="/en/signup/login" method="post">
                                        <input type="hidden" name="react" value="1" />
                                        <input type="hidden" name="_csrf" value={this.props._csrf} />
                                        <div className={"form-group " + (this.props.model.errors.email ? 'has-error' : '')}>
                                            <label className="label-control sr-only" htmlFor="userloginform-email">{this.props.model.attributeLabels.email}</label>
                                            <div className="form-group field-userloginform-email required has-success">
                                                <input id="userloginform-email" className="form-control" name="UserLoginForm[email]"
                                                       placeholder={this.props.model.attributeLabels.email} type="text" defaultValue={this.props.model.attributes.email} />
                                                <div className="help-block">{(this.props.model.errors.email ? this.props.model.errors.email.join('<br />') : '')}</div>
                                            </div>
                                        </div>

                                        <div className={"form-group " + (this.props.model.errors.password ? 'has-error' : '')}>
                                            <label className="label-control sr-only" htmlFor="userloginform-password">{this.props.model.attributeLabels.password}</label>
                                            <div className="form-group field-userloginform-password required has-success">
                                                <input id="userloginform-password" className="form-control" name="UserLoginForm[password]"
                                                       placeholder={this.props.model.attributeLabels.password} aria-required="true" type="password"  defaultValue={this.props.model.attributes.password} />
                                                <div className="help-block">{(this.props.model.errors.password ? this.props.model.errors.password.join('<br />') : '')}</div>
                                            </div>
                                        </div>

                                        <div className="control-group">
                                            <div className="controls">
                                                <a href={this.props.translations.test.url_forgot_password} className="ft-form-renewalink pull-right">{this.props.translations.test.link_forgot_password}</a>
                                            </div>
                                        </div>

                                        <div className={"form-group " + (this.props.model.errors.verifyCode ? 'has-error' : 'hide')}>
                                            <hr />
                                            <label className="control-label" htmlFor="inputVerifyCode"> {this.props.translations.test.verifycode} </label>
                                            <div className="form-group field-userloginform-verifycode">
                                                <img id="userloginform-verifycode-image" src={this.props.translations.test.url_captcha} alt="" />
                                                <input id="userloginform-verifycode" className="form-control" name="UserLoginForm[verifyCode]" placeholder={this.props.translations.test.verifycode_placeholder} type="text" />
                                                <div className="help-block"> </div>
                                            </div>
                                            <br />
                                            <hr />
                                        </div>
                                        
                                        <div className="form-group">
                                            <label>
                                                <div className="form-group field-userloginform-rememberme">
                                                    <input name="UserLoginForm[rememberMe]" value="0" type="hidden" />
                                                        <label>
                                                            <input id="userloginform-rememberme" name="UserLoginForm[rememberMe]" value="1" type="checkbox" defaultChecked={this.props.model.attributes.rememberMe}/> {this.props.model.attributeLabels.rememberMe}
                                                        </label>
                                                    <div className="help-block"> </div>
                                                </div>
                                            </label>
                                        </div>
                                        <button type="submit" className="btn btn-success btn-lg btn-block">{this.props.translations.test.button_login_to_dashboard}</button>
                                    </form>

                                </div>
                                <div className="ft-fullscreen-popbox-footer text-center">
                                    {this.props.translations.test.link_signup_label}
                                    <br />
                                    <a href={this.props.translations.test.url_signup}>
                                        <strong>
                                            {this.props.translations.test.link_signup}
                                        </strong>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}
window.Main = Main;
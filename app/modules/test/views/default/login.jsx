class Main extends React.Component {
    render() {
        let model = this.props.model;

        return (
            <div className="ft-fullscreen-bg">
                <div className="container">
                    <div className="row">
                        <div className="col-sm-6 col-sm-offset-3">
                            <h1>{this.props.translations.page_title}</h1>
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
                                        <div className={"form-group " + (model.errors.email ? 'has-error' : '')}>
                                            <label className="label-control sr-only" htmlFor="userloginform-email">{model.attributeLabels.email}</label>
                                            <div className="form-group field-userloginform-email required has-success">
                                                <input id="userloginform-email" className="form-control" name="UserLoginForm[email]"
                                                       placeholder={model.attributeLabels.email} type="text" defaultValue={model.attributes.email} />
                                                <div className="help-block">{(model.errors.email ? model.errors.email.join('<br />') : '')}</div>
                                            </div>
                                        </div>

                                        <div className={"form-group " + (model.errors.password ? 'has-error' : '')}>
                                            <label className="label-control sr-only" htmlFor="userloginform-password">{model.attributeLabels.password}</label>
                                            <div className="form-group field-userloginform-password required has-success">
                                                <input id="userloginform-password" className="form-control" name="UserLoginForm[password]"
                                                       placeholder={model.attributeLabels.password} aria-required="true" type="password"  defaultValue={model.attributes.password} />
                                                <div className="help-block">{(model.errors.password ? model.errors.password.join('<br />') : '')}</div>
                                            </div>
                                        </div>

                                        <div className="control-group">
                                            <div className="controls">
                                                <a href={this.props.translations.url_forgot_password} className="ft-form-renewalink pull-right">{this.props.translations.link_forgot_password}</a>
                                            </div>
                                        </div>

                                        <div className={"form-group " + (model.errors.verifyCode ? 'has-error' : 'hide')}>
                                            <hr />
                                            <label className="control-label" htmlFor="inputVerifyCode"> {this.props.translations.verifycode} </label>
                                            <div className="form-group field-userloginform-verifycode">
                                                <img id="userloginform-verifycode-image" src={this.props.translations.url_captcha} alt="" />
                                                <input id="userloginform-verifycode" className="form-control" name="UserLoginForm[verifyCode]" placeholder={this.props.translations.verifycode_placeholder} type="text" />
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
                                                            <input id="userloginform-rememberme" name="UserLoginForm[rememberMe]" value="1" type="checkbox" defaultChecked={model.attributes.rememberMe}/> {model.attributeLabels.rememberMe}
                                                        </label>
                                                    <div className="help-block"> </div>
                                                </div>
                                            </label>
                                        </div>
                                        <button type="submit" className="btn btn-success btn-lg btn-block">{this.props.translations.button_login_to_dashboard}</button>
                                    </form>

                                </div>
                                <div className="ft-fullscreen-popbox-footer text-center">
                                    {this.props.translations.link_signup_label}
                                    <br />
                                    <a href={this.props.translations.url_signup}>
                                        <strong>
                                            {this.props.translations.link_signup}
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
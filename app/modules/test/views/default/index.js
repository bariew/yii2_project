class Main extends React.Component {
  render() {
    return React.createElement("div", {
      className: "ft-fullscreen-bg"
    }, React.createElement("div", {
      className: "container"
    }, React.createElement("div", {
      className: "row"
    }, React.createElement("div", {
      className: "col-sm-6 col-sm-offset-3"
    }, React.createElement("h1", null, this.props.translations.page_title)))), React.createElement("div", {
      className: "container "
    }, React.createElement("div", {
      className: "row"
    }, React.createElement("div", {
      className: "col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3 col-xs-12 col-xs-offset-0"
    }, React.createElement("div", {
      className: "ft-fullscreen-popbox"
    }, React.createElement("div", {
      className: "ft-fullscreen-popbox-content"
    }, React.createElement("form", {
      id: "login-form",
      className: "",
      action: "/en/signup/login",
      method: "post"
    }, React.createElement("input", {
      type: "hidden",
      name: "react",
      value: "1"
    }), React.createElement("input", {
      type: "hidden",
      name: "_csrf",
      value: this.props._csrf
    }), React.createElement("div", {
      className: "form-group " + (this.props.model.errors.email ? 'has-error' : '')
    }, React.createElement("label", {
      className: "label-control sr-only",
      htmlFor: "userloginform-email"
    }, this.props.model.attributeLabels.email), React.createElement("div", {
      className: "form-group field-userloginform-email required has-success"
    }, React.createElement("input", {
      id: "userloginform-email",
      className: "form-control",
      name: "UserLoginForm[email]",
      placeholder: this.props.model.attributeLabels.email,
      type: "text",
      defaultValue: this.props.model.attributes.email
    }), React.createElement("div", {
      className: "help-block"
    }, this.props.model.errors.email ? this.props.model.errors.email.join('<br />') : ''))), React.createElement("div", {
      className: "form-group " + (this.props.model.errors.password ? 'has-error' : '')
    }, React.createElement("label", {
      className: "label-control sr-only",
      htmlFor: "userloginform-password"
    }, this.props.model.attributeLabels.password), React.createElement("div", {
      className: "form-group field-userloginform-password required has-success"
    }, React.createElement("input", {
      id: "userloginform-password",
      className: "form-control",
      name: "UserLoginForm[password]",
      placeholder: this.props.model.attributeLabels.password,
      "aria-required": "true",
      type: "password",
      defaultValue: this.props.model.attributes.password
    }), React.createElement("div", {
      className: "help-block"
    }, this.props.model.errors.password ? this.props.model.errors.password.join('<br />') : ''))), React.createElement("div", {
      className: "control-group"
    }, React.createElement("div", {
      className: "controls"
    }, React.createElement("a", {
      href: this.props.translations.url_forgot_password,
      className: "ft-form-renewalink pull-right"
    }, this.props.translations.link_forgot_password))), React.createElement("div", {
      className: "form-group " + (this.props.model.errors.verifyCode ? 'has-error' : 'hide')
    }, React.createElement("hr", null), React.createElement("label", {
      className: "control-label",
      htmlFor: "inputVerifyCode"
    }, " ", this.props.translations.verifycode, " "), React.createElement("div", {
      className: "form-group field-userloginform-verifycode"
    }, React.createElement("img", {
      id: "userloginform-verifycode-image",
      src: this.props.translations.url_captcha,
      alt: ""
    }), React.createElement("input", {
      id: "userloginform-verifycode",
      className: "form-control",
      name: "UserLoginForm[verifyCode]",
      placeholder: this.props.translations.verifycode_placeholder,
      type: "text"
    }), React.createElement("div", {
      className: "help-block"
    }, " ")), React.createElement("br", null), React.createElement("hr", null)), React.createElement("div", {
      className: "form-group"
    }, React.createElement("label", null, React.createElement("div", {
      className: "form-group field-userloginform-rememberme"
    }, React.createElement("input", {
      name: "UserLoginForm[rememberMe]",
      value: "0",
      type: "hidden"
    }), React.createElement("label", null, React.createElement("input", {
      id: "userloginform-rememberme",
      name: "UserLoginForm[rememberMe]",
      value: "1",
      type: "checkbox",
      defaultChecked: this.props.model.attributes.rememberMe
    }), " ", this.props.model.attributeLabels.rememberMe), React.createElement("div", {
      className: "help-block"
    }, " ")))), React.createElement("button", {
      type: "submit",
      className: "btn btn-success btn-lg btn-block"
    }, this.props.translations.button_login_to_dashboard))), React.createElement("div", {
      className: "ft-fullscreen-popbox-footer text-center"
    }, this.props.translations.link_signup_label, React.createElement("br", null), React.createElement("a", {
      href: this.props.translations.url_signup
    }, React.createElement("strong", null, this.props.translations.link_signup))))))));
  }

}

window.Main = Main;


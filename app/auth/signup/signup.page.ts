import { Component, OnInit, Injectable, NgModule, Self, Optional } from '@angular/core';
import { Router } from '@angular/router';
import { FormGroup, FormControl, Validators, FormBuilder, ReactiveFormsModule, FormsModule, NgControl } from '@angular/forms';
import { IonIntlTelInputValidators, IonIntlTelInputModel } from 'ion-intl-tel-input';
import { Comp } from '../../validators/password.validator';
import { AccessProviders } from '../../AccessProviders/access-providers';
import { ToastController, LoadingController, AlertController, NavController } from '@ionic/angular';

@Component({
  selector: 'app-signup',
  templateUrl: './signup.page.html',
  styleUrls: ['./signup.page.scss'],
})


@NgModule({
  imports: [ReactiveFormsModule, FormsModule]
  })
export class SignupPage implements OnInit {

  submitted = false;
  telphone: any;
  testform: boolean = false;

  constructor(
    @Optional()
    @Self()
    public controlDir: NgControl,
    private router: Router,
    public formBuilder: FormBuilder,
    private acc: AccessProviders,
    public navCtrl: NavController,
    private loadCtrl: LoadingController,
    private alertCtrl: AlertController,
    private toastCtrl: ToastController
  ) {
    if (controlDir) {
      this.controlDir.valueAccessor = this.controlDir.valueAccessor;
    }
  }

  ngOnInit() {
    
  }

  ionViewDidEnter() {
    this.signupForm.reset();
    this.testform = true;
  }

  get name() {
    return this.signupForm.get('name');
  }
  get username() {
    return this.signupForm.get('username');
  }
  get email() {
    return this.signupForm.get('email');
  }
  get pass() {
    return this.signupForm.get('pass');
  }
  get cpass() {
    return this.signupForm.get('cpass');
  }
  get phoneNumber() {
    return this.signupForm.get('phoneNumber');
  }

  public errorMessages = {
    name: [
      { type: 'required', message: 'Name is required' },
      { type: 'maxlength', message: 'Name cant be longer than 100 characters' },
      { type: 'minlength', message: 'Name cant be shorter than 6 characters' }
    ],
    email: [
      { type: 'required', message: 'Email is required' },
      { type: 'pattern', message: 'Please enter a valid email address' },
    ],
    username: [
      { type: 'required', message: 'Username is required' },
      { type: 'minlength', message: 'Username cant be shorter than 6 characters' },
      { type: 'maxlength', message: 'Username cant be longer than 12 characters' }
    ],
    pass: [
      { type: 'required', message: 'Password is required' },
      { type: 'minlength', message: 'Password cant be shorter than 6 characters' },
      { type: 'maxlength', message: 'Password cant be longer than 16 characters' }
    ],
    cpass: [
      { type: 'required', message: 'Confirm password is required' },
      { type: 'minlength', message: 'Confirm password cant be shorter than 6 characters' },
      { type: 'maxlength', message: 'Confirm password cant be longer than 16 characters' },
      { type: 'mustMatch', message: 'Confirm password is not same' }
    ],
    phoneNumber: [
      { type: 'required', message: 'Phone number is required' },
      { type: 'phone', message: 'Phone number pattern is incorrect' }
    ],
  };

  signupForm = this.formBuilder.group({
    name: ['', [Validators.required, Validators.minLength(6), Validators.maxLength(100)]],
    username: ['', [Validators.required, Validators.minLength(6), Validators.maxLength(12)]],
    email: ['', [Validators.required, Validators.pattern('[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$')]],
    pass: ['', [Validators.required, Validators.minLength(6), Validators.maxLength(16)]],
    cpass: ['', [Validators.required, Validators.minLength(6), Validators.maxLength(16)]],
    phoneNumber: ['', [Validators.required, IonIntlTelInputValidators.phone]]
  }, {
      validator: Comp("pass", "cpass")
    });

  dismiss() {
    this.router.navigate(['/signin']);
  }

  async signup() {
    this.telphone = JSON.stringify(this.phoneNumber.value, ['internationalNumber'], 1);
    console.log(JSON.stringify(this.phoneNumber.value));
    console.log(this.name.value);
    console.log(this.telphone.toString());
    const loader = await this.loadCtrl.create({
      mode: 'ios',
      message: "Please wait...",
    });

    loader.present();
    return new Promise(resolve => {
      let body = {
        action: 'signup',
        name: this.name.value,
        username: this.username.value,
        email: this.email.value,
        tel:this.phoneNumber.value,
        pass: this.pass.value
      }
      this.acc.postData(body, 'api.php').subscribe((res: any) => {
        if (res.success == true) {
          loader.dismiss();
          this.navCtrl.navigateRoot(['/success']);
        } else {
          loader.dismiss();
          this.presentToast(res.msg);
        }
      }, (err) => {
        loader.dismiss();
        this.presentAlert('Timeout');
      });
    });
  }

  async presentToast(a) {
    const toast = await this.toastCtrl.create({
      message: a,
      mode: 'ios',
      duration: 1500,
      position: 'bottom'
    });
    toast.present();
  }

  async presentAlert(a) {
    const alert = await this.alertCtrl.create({
      cssClass: 'my-custom-class',
      mode: 'ios',
      header: a,
      backdropDismiss: false,
      buttons: [
        {
          text: 'Close',
          handler: (blah) => {
            console.log('Confirm Cancel: blah');
          }
        }, {
          text: 'Try Again',
          handler: () => {
            this.signup();
          }
        }
      ]
    });

    await alert.present();
  }
}



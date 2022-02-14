import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Storage } from '@ionic/storage-angular';
import { ToastController, LoadingController, AlertController, NavController } from '@ionic/angular';
import { AccessProviders } from 'src/app/AccessProviders/access-providers';
import { AppComponent } from 'src/app/app.component';

@Component({
  selector: 'app-signin',
  templateUrl: './signin.page.html',
  styleUrls: ['./signin.page.scss'],
})
export class SigninPage implements OnInit {

  animatebased = false;
  animategrid = false;
  email: string;
  pass: string;

  constructor(
    private router: Router,
    private toastCtrl: ToastController,
    private loadCtrl: LoadingController,
    private alertCtrl: AlertController,
    private acc: AccessProviders,
    public navCtrl: NavController,
    private storage: Storage,
    private app: AppComponent
  ) { }

  ngOnInit() {
    this.animatebased = true;
    this.animategrid = true;
  }

  signup() {
    this.router.navigate(['/signup']);
  }

  async signin() {
    if (this.email == "") {
      this.presentToast('Email is required');
    } else if (this.pass == "") {
      this.presentToast('Password is required');
    } else {
      const loader = await this.loadCtrl.create({
        mode: 'ios',
        message: "Please wait...",
      });

      loader.present();

      return new Promise(resolve => {
        let body = {
          action: 'signin',
          email: this.email,
          pass: this.pass
        }

        this.acc.postData(body, 'api.php').subscribe((res: any) => {
          if (res.success == true) {
            loader.dismiss();
            if (res.confirm == 'YES') {
              this.storage.set('StorageUser', res.result);
              if (res.friend != null) {
                this.storage.set('Friend', res.friend);
              }
              if (res.friendrequest != null) {
                this.storage.set('FriendRequest', res.friendrequest);
              }
              this.app.initializeApp();
              this.navCtrl.navigateRoot(['dashboard']);
            } else if (res.confirm == 'NO') {
              this.presentAlert('Please verify your email first!');
            }
          } else {
            loader.dismiss();
            this.presentAlert('Email or password is wrong');
          }
        }, (err) => {
          loader.dismiss();
          this.presentAlert('Timeout');

          });
      });
    }
  }

  async presentToast(a) {
    const toast = await this.toastCtrl.create({
      message: a,
      mode: 'ios',
      cssClass: 'toastcolor',
      duration: 1500
    });
    toast.present();
  }

  async presentAlert(a) {
    const alert = await this.alertCtrl.create({
      header: a,
      mode: 'ios',
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
            this.signin();
          }
        }
      ]
    });

    await alert.present();
  }

}

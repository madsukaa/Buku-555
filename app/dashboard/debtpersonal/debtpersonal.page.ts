import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController, LoadingController, AlertController } from '@ionic/angular';
import { AccessProviders } from 'src/app/AccessProviders/access-providers';
import { Storage } from '@ionic/storage-angular';

@Component({
  selector: 'app-debtpersonal',
  templateUrl: './debtpersonal.page.html',
  styleUrls: ['./debtpersonal.page.scss'],
})
export class DebtpersonalPage implements OnInit {

  name: string;
  desc: string;
  duedate: string;
  type: string;
  amount = 0;
  id: string;
  datastorage: any;

  constructor(
    private router: Router,
    private toastCtrl: ToastController,
    private acc: AccessProviders,
    private loadCtrl: LoadingController,
    private alertCtrl: AlertController,
    private storage: Storage
  ) { }

  ngOnInit( ) {
    this.storage.get('StorageUser').then((res) => {
      if (res != null) {
        this.datastorage = res;
        this.id = this.datastorage[0].user_id;
        console.log(this.id);
      }
    });
  }

  typeOption: any = {
    Header: 'Select your type',
    mode: 'ios'
  }

  inc() {
    this.amount++;
  }

  dec() {
    if (this.amount > 0) {
      this.amount--;
    } 
    
  }

  dismiss() {
    this.router.navigate(['/dashboard/dashboard/adddebt']);
  }

  async adddebt() {
    if (this.name == null) {
      this.presentToast("Please enter name or title");
    } else if (this.desc == null) {
      this.presentToast("Please enter description!");
    } else if (this.amount == null) {
      this.presentToast("Please enter amount");
    } else if (this.duedate == null) {
      this.presentToast("Please enter the duedate");
    } else if (this.type == null) {
      this.presentToast("Please enter type of debt");
    } else {
      const loader = await this.loadCtrl.create({
        mode: 'ios'
      });

      loader.present();
      return new Promise(resolve => {
        let body = {
          action: 'addpersonal',
          name: this.name,
          desc: this.desc,
          amount: this.amount,
          duedate: this.duedate,
          type: this.type,
          id: this.id
        }
        this.acc.postData(body, 'api.php').subscribe((res: any) => {
          if (res.success == true) {
            loader.dismiss();
            this.presentA(res.msg);
          } else {
            loader.dismiss();
            this.presentToast(res.msg);
          }
        }, (err) => {
          loader.dismiss();
          this.presentAlert('No internet connection');
        });
      });
    }
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
            this.adddebt();
          }
        }
      ]
    });

    await alert.present();
  }

  async presentA(a) {
    const alert = await this.alertCtrl.create({
      cssClass: 'my-custom-class',
      mode: 'ios',
      header: a,
      buttons: [
         {
          text: 'Okay',
          handler: () => {
            this.router.navigate(['/dashboard/dashboard/adddebt']);
          }
        }
      ]
    });

    await alert.present();
  }

}

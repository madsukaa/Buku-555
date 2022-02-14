import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AccessProviders } from 'src/app/AccessProviders/access-providers';
import { Storage } from '@ionic/storage-angular';
import { ActionSheetController, LoadingController, ToastController } from '@ionic/angular';
import { AppComponent } from 'src/app/app.component';

@Component({
  selector: 'app-addfriend',
  templateUrl: './addfriend.page.html',
  styleUrls: ['./addfriend.page.scss'],
})
export class AddfriendPage implements OnInit {

  search: string;
  users: any = [];
  checkuser: boolean = false;
  username: string;
  id: string = "";
  datastorage: any;

  constructor(
    private router: Router,
    private acc: AccessProviders,
    private storage: Storage,
    private actSheet: ActionSheetController,
    private loadCtrl: LoadingController,
    private app: AppComponent,
    private toastCtrl: ToastController
  ) { }

  ngOnInit() {
  }

  ionViewDidEnter() {
    this.username = this.app.getusername();
    this.users = [];
    this.storage.get('StorageUser').then((res) => {
      if (res != null) {
        this.datastorage = res;
        this.id = this.datastorage[0].user_id;
        console.log(this.id);
      }
    });

    
  }

  dismiss() {
    this.router.navigate(['/dashboard/dashboard/history']);
  }

  filterList(e) {
   
    this.search = e.srcElement.value;
    this.users = [];
    this.loaduser(this.search);
  }

  async loaduser(e) {
    
    if (e == this.username) {
      return;
    } else {
      return new Promise(resolve => {
        let body = {
          action: 'loaduser',
          username: e,
          id: this.app.getid()
        }

        this.acc.postData(body, 'api.php').subscribe((res: any) => {
          if (res.success == true) {
            for (let user of res.result) {
              this.users.push(user);
              this.checkuser = true;
            }
          } else {

          }

          resolve(true);
        });
      });
    }
    
  }

  async request(a) {
    const actionSheet = await this.actSheet.create({
      mode: 'ios',
      cssClass: 'requestfriend',
      buttons: [{
        text: 'Send Request',
        icon: 'send',
        handler: () => {
          this.requestuser(a);
          
        }
      }, {
          text: 'Cancel',
          icon: 'close',
          role: 'cancel',
          handler: () => {

          }
        }]
    });

    await actionSheet.present();

    const { role } = await actionSheet.onDidDismiss();
  }

  async requestuser(a) {
    const loader = await this.loadCtrl.create({
    });
    loader.present();

    return new Promise(resolve => {
      let body = {
        action: 'requestfriend',
        friendid: a,
        id: this.app.getid()
      }
      console.log(this.app.getid());
      this.acc.postData(body, 'api.php').subscribe((res: any) => {
        if (res.success == true) {
          loader.dismiss();
          this.presentToast(res.msg);
          this.ionViewDidEnter();
        } else {
          loader.dismiss();
          this.presentToast(res.msg);
          this.ionViewDidEnter();
        }
      });
    });
  }

  async presentToast(a) {
    const toast = await this.toastCtrl.create({
      mode: 'ios',
      message: a,
      cssClass: 'toastcolor',
      duration: 1500
    });
    toast.present();
  }

}

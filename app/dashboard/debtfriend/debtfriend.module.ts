import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { DebtfriendPageRoutingModule } from './debtfriend-routing.module';

import { DebtfriendPage } from './debtfriend.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    DebtfriendPageRoutingModule
  ],
  declarations: [DebtfriendPage]
})
export class DebtfriendPageModule {}

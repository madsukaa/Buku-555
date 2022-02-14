import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { DebtpersonalPageRoutingModule } from './debtpersonal-routing.module';

import { DebtpersonalPage } from './debtpersonal.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    DebtpersonalPageRoutingModule
  ],
  declarations: [DebtpersonalPage]
})
export class DebtpersonalPageModule {}

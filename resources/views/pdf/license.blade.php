<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>License for the Occupation of Government Premises</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 5px;
            vertical-align: top;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-bold {
            font-weight: bold;
        }
        .header {
            font-size: 18px;
            font-weight: bold;
        }
        .subheader {
            font-size: 16px;
            font-weight: bold;
        }
        .terms {
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div>
        <table align="center" width="100%">
            <tr>
                <td align="center" colspan="2">
                    <div class="header">GOVERNMENT OF WEST BENGAL</div>
                    <div class="subheader">HOUSING DEPARTMENT</div>
                    <div class="subheader">Licence for the Occupation of Government Premises</div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br></td>
            </tr>
            <tr>
                <td>No. {{ $licenseDetails->license_no }}</td>
                <td align="right">Date : {{ $licenseDetails->license_issue_date }}</td>
            </tr>
            <tr>
                <td colspan="2"><br><br></td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>
                        Licence under section 4 of the West Bengal Government Premises (Regulation of Occupancy ) Act, 1984 is hereby granted to {{ $licenseDetails->gender_prefix }} <b>{{ $licenseDetails->applicant_name }}, {{ $licenseDetails->applicant_designation }}</b> for the occupation of flat no. <b>{{ $licenseDetails->flat_no }}, {{ $licenseDetails->allotment_estate }}, {{ $licenseDetails->allotment_address }}, {{ $licenseDetails->allotment_district }}</b> subject to the provisions of the said Act and to the additional terms and conditions mentioned below.<br><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. The validity of this licence will expire on <b>{{ $licenseDetails->license_expiry_date }}.</b><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. Date of Retirement <b>{{ $licenseDetails->date_of_retirement ?: 'N/A' }}.</b>
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br><br></td>
            </tr>
            <tr>
                <td align="right" colspan="2">
                    <table>
                        <tr>
                            <td align="center">
                                Competent Authority under the West Bengal<br> 
                                Government Premises (Regulation of Occupancy)<br>
                                Act, 1984
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <div class="subheader">ADDITIONAL TERMS AND CONDITIONS</div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br><br></td>
            </tr>
            <tr>
                <td colspan="2">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. He/She will not draw H. R. A.<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. He/She is requested to intimate this Department the date of possession of the flat.
                </td>
            </tr>
            <tr>
                <td colspan="2"><br><br><br></td>
            </tr>
            <tr>
                <td align="right" colspan="2">
                    <table>
                        <tr>
                            <td align="center">
                                Competent Authority under the West Bengal<br> 
                                Government Premises (Regulation of Occupancy)<br>
                                Act, 1984
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br></td>
            </tr>
            <tr>
                <td>No. {{ $licenseDetails->license_no }}</td>
                <td align="right">Date : {{ $licenseDetails->license_issue_date }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Copy forwarded, for information and necessary action to :<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(i) {{ $licenseDetails->gender_prefix }} <b>{{ $licenseDetails->applicant_name }}</b><br>																										
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ii) <b>{{ $licenseDetails->office_name }}</b><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(iii) <b>{{ $licenseDetails->ddo_designation }}, {{ $licenseDetails->ddo_address }}</b><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;He is requested to intimate this office regarding transfer/retirement of the licensee from the present posting.<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(iv) The Executive Engineer, Housing Construction Division No. .................................................<br>	
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(v) The Executive Engineer, Electrical Division No. ......................................................................<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(vi) The Pay and Accounts Officer, Kolkata Pay and Accounts Office,81/2/2, Phears Lane, Kolkata -12<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;or Treasury Officer .......................................................................................................................<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(vii) The Work Assistant .................................................................................................................<br>	
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;It may be noted that the retirement of the licensee is on ............................................................<br> 	
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(viii) The Assistant Engineer, Housing Construction Sub-Division No. .........................................<br> 	
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ix) The Computer Cell<br><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. Special attention is invited to clauses 10 and 11 of the terms and conditions mentioned overleaf. Pending receipt of roll for licence fee from the Executive Engineer, licence fee at the rate indicated under additional terms and conditions should be realised from the salary of the licensee and credited to the head "0216-Housing-02-Urban Housing-105-Receipts from Rental Housing Scheme--001 collection for RHS-05-Rent". Code No. [0216-02-105-001-05]
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br><br></td>
            </tr>
            <tr>
                <td align="right" colspan="2">
                    <table>
                        <tr>
                            <td align="center">
                                Competent Authority under the West Bengal<br> 
                                Government Premises (Regulation of Occupancy)<br>
                                Act, 1984
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br></td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Terms and conditions applicable to all licensees in respect of Government Premises</b>
                </td>
            </tr>
            <tr>
                <td colspan="2"><br><br></td>
            </tr>
            <tr>
                <td colspan="2">
                    <p class="terms">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. Subject to the provisions of item 2 and section 11, a license shall remain valid for the specified period and such period may be renewed from time to time by the Competent Authority. For this purpose specified period shall mean - <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(i) the period specified in the license or, where nothing is specified in the license, a period of three years from the date of issue of license and such date, in the case of anything deemed to be a license under section 9, shall be the appointed day ; or <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ii) any period for which the license is renewed by the Competent Authority.<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. An application for the renewal of a license shall be made to the Competent Authority in the prescribed manner. No such application shall be refused by the Competent Authority if the licensee continues to be eligible to hold the license in accordance with the provisions of this Act. A license-shall remain valid during the pendency of an application for its renewal. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. An application for the renewal of a license shall state inter alia the licensee's post and place of posting. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. A license shall cease to be valid if the licensee fails to take possession of the premises covered by it (hereinafter referred to as the premises) within fifteen days of its issue or within such period as the Competent Authority may grant, upon an application by the licensee. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5. The licensee shall use the premises for the purpose for which they have been allotted to him and shall not use them for any other purpose.<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Example- Where a flat is allotted to any person for the purpose of his residence there will be a violation of this condition if he dose not himself ordinarily, reside in it and allows his dependents or any other person to reside in it or keeps it vacant. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;6. The licensee shall not assign or transfer the premises in any way to any person or put any person in possession of the premises. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;7. The licensee shall not add to, or alter, any fixtures of the premises or make any structural alterations in the premises without the express permission in writing of the Competent Authority. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;8. The licensee shall not cause, or suffer to be caused, any damage to the premises beyond the normal wear and tear through the proper use and occupation of the premises. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;9. The licensee shall allow any officer duly authorised in this behalf by the Competent Authority to inspect the premises as and when necessary. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;10. The licensee shall pay such license fee for the premises as may be determined from time to time by the Competent Authority. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;11. The license fee in respect of each month shall be payable by the first week of the following month in such manner as may be stipulated by the Competent Authority. The licensee shall send to the Competent Authority as and when required by it a statement showing the details of the license fee paid by the licensee, and such statement shall be in such form, and shall be authenticated in such manner, as may be stipulated by the Competent Authority. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;12. The licensee, if he is in occupation of Government premises on the appointed day, shall inform the Competent Authority in writing within one month from that day about the post held by him, the headquarters of his post and the date of his retirement on superannuation based upon the entries contained in his Service Book. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;13. The licensee shall inform the Competent Authority in writing about every change in his post or place of posting and every change in his is by reason of his going on leave, or being placed under suspension or by any other reason within a week of the change taking place. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;14. A license may be terminated upon--<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(a) the acquisition of a licensee of any other premises by way of ownership, tenancy or otherwise either in his own name or in the name of any member of family dependent on him. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(b) the violation by the licensee of any of the terms and conditions of the license; <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(c) the licensee being placed under suspension, or upon proceeding on leave of any kind, for a period exceeding six months: <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Provided that where, by reason of any declaration made under section 5, that post from which the licensee has proceeded on leave is a specified post in respect of the premises occupied by him and the authority granting the leave has not certified that upon the expiry of the leave the licensee is likely to return to that post, the license may be terminated at any time after the commencement of the leave; <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(d) the expiry of one calendar month's notice given by the Competent Authority to the licensee. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;15. Upon the expiry or termination of any license, the licensee shall deliver vacant possession of the premises to the Competent Authority, or to any other person authorised by the Competent Authority in this behalf, in the same condition in which the licensee took possession of the premises. <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;16. The license shall automatically terminated upon : <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(i) The death of the licensee or (ii) the expiry of the period of validity of the license or (iii) the cessation of the licensee's employment under the state Government by reason of his retirement, resignation, discharge or dismissal or by any other reason, or (iv) the licensee ceasing to hold any specified post under the state Government by reason of his transfer from any such post on any other reason.<br><br>
                        SPL/12-13
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

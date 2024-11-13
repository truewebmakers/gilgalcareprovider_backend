


<table style="text-align:left;width: 100%;  max-width:500px;background-color:#f8f8f8;border-radius:3px;font-family:helvetica;" cellpadding="10">
    <tr>
      <td colspan="2" style="text-align:center;font-weight: bold; font-size: 20px; color: #E12E47;padding-top:30px;" align="center">New Entry Submission</td>
    </tr>
    @foreach($data as $key => $p)
     <tr>
       <th width='150px'>{{$key}}</th>
       <td>{{ $p }}</td>
     </tr>
    @endforeach
  </table>

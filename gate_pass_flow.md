# Gate Pass Flow in System

Here is the complete systemic flow of how a **Gate Pass** is handled in the Stonecrusher ERP.

```mermaid
graph TD
    %% Initiators & Constraints
    A([User Configures Gate Pass]) --> B{Day Closure Check}
    B -- Closed Date --> Z((Error: Cannot Edit/Create))
    B -- Open Date --> C{Select Status?}

    %% Path 1: Pending
    C -- "Pending" --> P1[Fill Basic Details: \nVehicle, Client, Date, etc.]
    P1 --> P2[If Manual Vehicle entered,\nSystem creates new Vehicle]
    P2 --> P3[Save as 'Pending']
    P3 --> S1(((State: \nPENDING)))
    
    %% Transition to other states
    S1 --> U1[User Edits Gate Pass]
    U1 --> B

    %% Path 2: Completed
    C -- "Completed" --> C1[Fill All Details: \nMetal Type, Weights, Rates, Diesel, Transport]
    C1 --> C2[System calculates: Net Weight]
    C2 --> C3{Is it an Internal Project?}
    C3 -- Yes --> C4[Override Total Amount to 0]
    C3 -- No --> C5[System calculates :\n(Qty * Rate) + Diesel + Transport]
    C4 --> C6[Save Location/Distance if requested]
    C5 --> C6
    C6 --> C7[Save as 'Completed']
    C7 --> S2(((State: \nCOMPLETED)))
    
    %% Actions following Completion
    S2 --> E1[Event: GatePassCompleted]
    E1 --> T1[Sales Service creates or \nupdates ClientTransaction]
    
    S2 --> AddPayment[Action: Record Payment]
    AddPayment --> B2{Day Closure Check}
    B2 -- Open --> T2[Sales Service records \npayment & affects Wallet]
    B2 -- Closed --> Z

    %% Path 3: Cancelled
    C -- "Cancelled" --> X1{Is User Admin?}
    X1 -- No --> Z2((Error: Only Admins\ncan cancel))
    X1 -- Yes --> X2[Save as 'Cancelled']
    X2 --> S3(((State: \nCANCELLED)))
    
    %% Actions following Cancellation
    S3 --> E2[Event: GatePassCancelled]
    E2 --> T3[Sales Service cancels \nassociated Transaction]
    
    %% Styling
    classDef state fill:#f9f,stroke:#333,stroke-width:2px;
    class S1,S2,S3 state;
```
